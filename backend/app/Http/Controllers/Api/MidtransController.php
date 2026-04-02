<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Payment;
use App\Models\User;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Kunci Midtrans (Ambil dari .env)
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * LANGKAH A: Membuat Snap Token untuk dikirim ke Flutter
     */
    public function getSnapToken(Request $request)
    {
        // 1. Buat Order ID Unik (Misal: SPK-17119922)
        $order_id = 'SPK-' . time();

        // 2. Siapkan Parameter Transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int) $request->total_bayar, // Harga setelah diskon promo
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
            ],
            // Item yang dibeli
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

            // 4. Simpan data transaksi ke DB dengan status 'pending'
            Payment::create([
                'transaction_code' => $order_id,
                'user_id' => $request->user_id,
                'program_id' => $request->program_id,
                'total_bayar' => $request->total_bayar,
                'status' => 'pending',
            ]);

            return response()->json(['token' => $snapToken, 'order_id' => $order_id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * LANGKAH B: Callback (Notifikasi Otomatis dari Midtrans)
     * Fungsi ini dipanggil Midtrans di "belakang layar" saat siswa selesai bayar.
     */
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Validasi agar data benar-benar dari Midtrans
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                // UPDATE STATUS JADI LUNAS OTOMATIS
                $payment = Payment::where('transaction_code', $request->order_id)->first();
                if ($payment) {
                    $payment->update([
                        'status' => 'verified',
                        'verified_at' => now()
                    ]);
                    
                    // Beri akses kelas ke siswa di sini (Enrollment)
                }
            }
        }
        return response()->json(['status' => 'OK']);
    }
}