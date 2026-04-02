<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo; 
use App\Models\ClassModel; 
use Carbon\Carbon; // Digunakan untuk manajemen waktu yang lebih akurat

class PembayaranController extends Controller {

    /**
     * Menampilkan halaman Manajemen Promo di Dashboard Admin
     */
    public function promo() {
        // Eager Load 'classModel' agar nama kelas muncul di tabel tanpa error
        $promos = Promo::with('classModel')->orderBy('created_at', 'desc')->get(); 
        
        // Mengambil semua kelas dari tabel programs (id & title)
        $classes = ClassModel::all(); 
        
        return view('admin.promo.index', compact('promos', 'classes'));
    }

    /**
     * Menyimpan Promo Baru (Aksi dari Form Admin)
     */
    public function storePromo(Request $request) {
        $request->validate([
            'kode_promo' => 'required|unique:promos,kode_promo|max:50',
            'diskon'     => 'required|numeric|min:0',
            'tipe_diskon'=> 'required|in:percentage,fixed',
            'kuota'      => 'required|integer|min:1',
            'start_date' => 'required|date',
            'expired'    => 'required|date|after_or_equal:start_date',
            'class_id'   => 'nullable|exists:programs,id', // Merujuk ke table programs
        ]);

        Promo::create([
            'kode_promo' => strtoupper($request->kode_promo), // Paksa huruf besar
            'diskon'     => $request->diskon,
            'tipe_diskon'=> $request->tipe_diskon,
            'kuota'      => $request->kuota,
            'start_date' => $request->start_date,
            'expired'    => $request->expired,
            'class_id'   => $request->class_id, // NULL jika Global
            'is_active'  => true,
            'used_count' => 0,
        ]);

        return redirect()->back()->with('success', 'Kode Promo Berhasil Diterbitkan!');
    }

    /**
     * Menghapus Promo
     */
    public function destroyPromo($id) {
        $promo = Promo::findOrFail($id);
        $promo->delete();
        return redirect()->back()->with('success', 'Promo Berhasil Dihapus');
    }
    
    /**
     * LOGIKA UTAMA: Cek Promo dari Aplikasi Mobile
     * Digunakan saat siswa menekan tombol "CEK" di halaman pendaftaran
     */
    public function checkPromo(Request $request) {
        $now = Carbon::now();

        // 1. Cari Promo (Harus Aktif, Sudah Mulai, dan Belum Expired)
        $promo = Promo::where('kode_promo', strtoupper($request->kode_promo))
                      ->where('is_active', true)
                      ->whereDate('start_date', '<=', $now)
                      ->whereDate('expired', '>=', $now)
                      ->first();

        if (!$promo) {
            return response()->json([
                'success' => false, 
                'message' => 'Kode promo tidak ditemukan, belum aktif, atau sudah kadaluarsa.'
            ]);
        }

        // 2. Cek Kuota Pemakaian
        if ($promo->used_count >= $promo->kuota) {
            return response()->json([
                'success' => false, 
                'message' => 'Maaf, kuota pemakaian promo ini sudah habis.'
            ]);
        }

        // 3. Logika Sikon: Global vs Spesifik Kelas
        if ($promo->class_id !== null) {
            // Jika promo ini punya target kelas, bandingkan dengan ID kelas yang didaftar siswa
            if ($promo->class_id != $request->class_id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Maaf, kode promo ini tidak berlaku untuk program kelas ini.'
                ]);
            }
        }

        // 4. Jika semua valid, kirim data diskon ke Flutter
        return response()->json([
            'success' => true,
            'tipe'    => $promo->tipe_diskon, // 'percentage' atau 'fixed'
            'nilai'   => $promo->diskon,
            'message' => 'Promo berhasil diterapkan! Total biaya sudah diperbarui.'
        ]);
    }

    /**
     * FUNGSI BARU: Ambil Daftar Promo yang tersedia untuk Kelas tertentu
     * Gunakan ini untuk memunculkan teks "Promo Tersedia" di halaman Detail Program HP
     */
    public function getPromosForClass($class_id) {
        $now = Carbon::now();

        $availablePromos = Promo::where('is_active', true)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('expired', '>=', $now)
            ->where(function($query) use ($class_id) {
                $query->whereNull('class_id') // Ambil yang Global
                      ->orWhere('class_id', $class_id); // ATAU yang khusus kelas ini
            })
            ->get(['kode_promo', 'diskon', 'tipe_diskon']);

        return response()->json([
            'success' => true,
            'data'    => $availablePromos
        ]);
    }
}