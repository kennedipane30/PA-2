<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Payment;
use App\Models\Enrollment; // Pastikan ini di-import
use App\Models\Promo;      // Pastikan ini di-import
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

        // Logika Promo (Tetap Seperti Kode Anda)
        $diskon = 0;
        if ($request->promo_code) {
            $promo = Promo::where('kode_promo', strtoupper($request->promo_code))
                                      ->where('is_active', true)
                                      ->first();
            if ($promo) {
                $hargaAsli = $request->harga_asli ?? $request->total_bayar;
                $diskon = ($promo->tipe_diskon == 'percentage') 
                         ? ($hargaAsli * $promo->diskon / 100) 
                         : $promo->diskon;
                $promo->increment('used_count');
            }
        }

        // 2. Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Gunakan Order ID yang unik agar Nomor VA Selalu Muncul Baru
        $order_id = 'SPK-' . time() . '-' . $request->user_id . '-' . rand(100, 999);

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int) $request->total_bayar,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email ?? 'siswa@spekta.com',
            ],
            // Memaksa Midtrans menampilkan metode VA dengan benar
            'enabled_payments' => ['bca_va', 'permata_va', 'bni_va', 'bri_va', 'gopay', 'shopeepay'],
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
            $snapToken = Snap::getSnapToken($params);

            Payment::create([
                'transaction_code' => $order_id,
                'user_id'          => $request->user_id,
                'program_id'       => $request->program_id,
                'snap_token'       => $snapToken,
                'harga_asli'       => (int) ($request->harga_asli ?? $request->total_bayar),
                'diskon'           => $diskon,
                'total_bayar'      => (int) $request->total_bayar,
                'promo_code'       => $request->promo_code,
                'status'           => 'pending',
            ]);

            return response()->json([
                'status' => 'success',
                'token' => $snapToken, 
                'order_id' => $order_id
            ]);

        } catch (\Exception $e) {
            Log::error("MIDTRANS ERROR: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $signature = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($signature != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order_id = $request->order_id;
        $status = $request->transaction_status;
        $payment = Payment::where('transaction_code', $order_id)->first();

        if (!$payment) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($status == 'settlement' || $status == 'capture') {
            $payment->update(['status' => 'verified']);

            // Berikan Akses Kelas Otomatis
            Enrollment::updateOrCreate(
                [
                    'user_id' => $payment->user_id,
                    'program_id' => $payment->program_id
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now()
                ]
            );

            Log::info("User ID {$payment->user_id} Berhasil join Kelas ID {$payment->program_id}");

        } elseif ($status == 'expire' || $status == 'cancel') {
            $payment->update(['status' => 'rejected']);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * 3. FUNGSI CEK STATUS (WAJIB ADA agar Centang Hijau di Flutter Berhasil Muncul)
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'program_id' => 'required',
        ]);

        $isEnrolled = Enrollment::where('user_id', $request->user_id)
                        ->where('program_id', $request->program_id)
                        ->where('status', 'active')
                        ->exists();

        return response()->json([
            'has_access' => $isEnrolled,
            'message' => $isEnrolled ? 'Akses ditemukan' : 'Akses belum aktif'
        ]);
    }
}