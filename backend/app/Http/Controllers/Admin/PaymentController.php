<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Fungsi untuk Siswa mengupload bukti bayar dari Mobile (API)
     */
    public function uploadBukti(Request $request)
    {
        // 1. Validasi Input dari Flutter
        $request->validate([
            'user_id'      => 'required',
            'program_id'   => 'required',
            'nama_pengirim'=> 'required|string',
            'total_bayar'  => 'required|numeric',
            'bukti_bayar'  => 'required|image|mimes:jpg,png,jpeg|max:3072', // Max 3MB
        ]);

        try {
            // 2. Proses Simpan File Gambar
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                // Nama file unik: bukti_123456789.jpg
                $filename = 'bukti_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke folder: storage/app/public/pembayaran
                $path = $file->storeAs('public/pembayaran', $filename);

                // 3. Simpan data ke Database
                $payment = Payment::create([
                    'transaction_code' => 'SPK-' . strtoupper(Str::random(8)), // Kode unik otomatis
                    'user_id'          => $request->user_id,
                    'program_id'       => $request->program_id,
                    'nama_pengirim'    => $request->nama_pengirim,
                    'harga_asli'       => $request->harga_asli ?? $request->total_bayar,
                    'diskon'           => $request->diskon ?? 0,
                    'total_bayar'      => $request->total_bayar,
                    'bukti_bayar'      => $filename, // Simpan nama filenya saja
                    'status'           => 'pending',
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Bukti berhasil dikirim, mohon tunggu verifikasi admin.',
                    'data'    => $payment
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}