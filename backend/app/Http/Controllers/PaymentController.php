<?php

namespace App\Http\Controllers;

use App\Models\Payment; 
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * FUNGSI 1: Membuat Token untuk Flutter
     */
    public function getSnapToken(Request $request)
    {
        // 1. Set Konfigurasi
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'INV-' . time() . '-' . rand();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId, 
                'gross_amount' => (int) $request->total_bayar, 
            ],
            'customer_details' => [
                'first_name' => $request->nama_lengkap,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // SIMPAN KE DATABASE
            Payment::create([
                'transaction_code' => $orderId,
                'user_id'          => Auth::id() ?? 1,
                'program_id'       => $request->program_id ?? $request->class_id,
                'snap_token'       => $snapToken,
                'harga_asli'       => $request->harga_asli ?? $request->total_bayar,
                'diskon'           => $request->diskon ?? 0,
                'total_bayar'      => $request->total_bayar,
                'status'           => 'pending',
            ]);

            return response()->json(['status' => 'success', 'token' => $snapToken, 'order_id' => $orderId]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'DB Error: ' . $e->getMessage() 
            ], 500);
        }
    }

    /**
     * FUNGSI 2: Menerima Notifikasi Otomatis dari Midtrans (Webhook)
     */
   public function handleNotification(Request $request) 
{
    $serverKey = env('MIDTRANS_SERVER_KEY');
    
    // Ambil data dari Midtrans
    $orderId = $request->order_id;
    $status = $request->transaction_status;
    $grossAmount = $request->gross_amount;
    $statusCode = $request->status_code;
    $signatureKey = $request->signature_key;

    // Verifikasi signature (WAJIB)
    $hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

    if ($hashed !== $signatureKey) {
        return response()->json(['message' => 'Invalid signature'], 403);
    }

    // Cari data payment
    $payment = Payment::where('transaction_code', $orderId)->first();

    if (!$payment) {
        return response()->json(['message' => 'Payment not found'], 404);
    }

    // 🔥 INI BAGIAN PENTING (FIX KAMU)
    if ($status == 'settlement' || $status == 'capture') {
        $payment->update([
            'status' => 'verified',
        ]);
    } elseif ($status == 'pending') {
        $payment->update([
            'status' => 'pending'
        ]);
    } elseif ($status == 'expire' || $status == 'cancel' || $status == 'deny') {
        $payment->update([
            'status' => 'rejected'
        ]);
    }

    Log::info("Midtrans status: $status for Order ID: $orderId");

    return response()->json(['status' => 'ok']);
}

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $statusResponse = Transaction::status($request->order_id);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        $payment = Payment::where('transaction_code', $request->order_id)->first();
        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        $status = $statusResponse->transaction_status ?? $statusResponse->status ?? null;

        if ($status == 'settlement' || $status == 'capture') {
            $payment->update(['status' => 'verified']);
        } elseif ($status == 'pending') {
            $payment->update(['status' => 'pending']);
        } elseif ($status == 'expire' || $status == 'cancel' || $status == 'deny') {
            $payment->update(['status' => 'rejected']);
        }

        return response()->json(['status' => 'success', 'data' => $statusResponse, 'payment_status' => $payment->status]);
    }
}
