<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo; // Pastikan Anda sudah buat Model Promo

class PromoController extends Controller
{
    // Menampilkan halaman promo
    public function index() {
        $promos = Promo::all();
        return view('admin.promo.index', compact('promos'));
    }

    // Fungsi untuk Admin Simpan Promo Baru
    public function store(Request $request) {
        Promo::create($request->all());
        return redirect()->back()->with('success', 'Promo berhasil dibuat!');
    }

    // Fungsi Logika "Otomatis Terpotong" untuk Siswa (Yang Anda tanyakan)
    public function applyPromo(Request $request) {
        $kode = $request->kode_promo;
        $hargaAsli = 900000;

        $promo = Promo::where('kode_promo', $kode)
                      ->where('is_active', true)
                      ->where('kuota', '>', \DB::raw('used_count'))
                      ->whereDate('expired', '>=', now())
                      ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode tidak valid!']);
        }

        // Hitung diskon
        $potongan = ($promo->tipe_diskon == 'percentage') 
                    ? ($hargaAsli * $promo->diskon / 100) 
                    : $promo->diskon;

        return response()->json([
            'success' => true,
            'potongan' => $potongan,
            'total_akhir' => $hargaAsli - $potongan
        ]);
    }
}