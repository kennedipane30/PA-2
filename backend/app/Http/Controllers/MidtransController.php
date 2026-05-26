<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function getSnapToken(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'user_id' => 'required',
            'program_id' => 'required',
            'total_bayar' => 'required',
            'name' => 'required',
        ]);

        // 2. Konfigurasi Midtrans (Taruh di sini agar pasti terbaca)
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $order_id = 'SPK-' . time() . rand(1000,9999);

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int) $request->total_bayar,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email ?? 'siswa@spekta.com',
            ],
            'item_details' => [
                [
                    'id' => $request->program_id,
                    'price' => (int) $request->total_bayar,
                    'quantity' => 1,
                    'name' => 'Program Spekta Academy',
                ]
            ]
        ];

        try {
            // 3. Minta Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // 4. SIMPAN KE DATABASE (Pastikan nama kolom SAMA dengan Migrasi)
            Payment::create([
                'transaction_code' => $order_id,
                'user_id'          => $request->user_id,
                'program_id'       => $request->program_id,
                'snap_token'       => $snapToken,
                'harga_asli'       => (int) $request->total_bayar,
                'diskon'           => 0,
                'total_bayar'      => (int) $request->total_bayar,
                'status'           => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'token' => $snapToken, 
                'order_id' => $order_id
            ]);

        } catch (\Exception $e) {
            // Catat error di log Laravel (storage/logs/laravel.log)
            Log::error("MIDTRANS ERROR: " . $e->getMessage());

            // Kirim pesan error ASLI ke Flutter agar kita tahu penyebabnya
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() 
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        // Validasi signature
        $signature = hash("sha512",
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($signature != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Ambil data
        $order_id = $request->order_id;
        $status = $request->transaction_status;

        Log::info("MIDTRANS CALLBACK:", $request->all());

        // Cari data di DB
        $payment = Payment::where('transaction_code', $order_id)->first();

        if (!$payment) {
            Log::error("Payment tidak ditemukan: " . $order_id);
            return response()->json(['message' => 'Not found'], 404);
        }

        // Mapping status
        if ($status == 'settlement' || $status == 'capture') {
            $payment->update([
                'status' => 'verified',
            ]);
        } elseif ($status == 'pending') {
            $payment->update([
                'status' => 'pending'
            ]);
        } elseif ($status == 'expire') {
            $payment->update([
                'status' => 'rejected'
            ]);
        } elseif ($status == 'cancel' || $status == 'deny') {
            $payment->update([
                'status' => 'rejected'
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}