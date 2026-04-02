<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
// Hapus atau comment baris yang menyebabkan error jika modelnya memang belum ada
// use App\Models\Classes; 
// use App\Models\Payment;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data dasar dari User (Ini pasti ada)
        // Kita gunakan role_id jika Anda menggunakan angka, atau whereHas jika menggunakan tabel roles
        $total_siswa = User::whereHas('role', fn($q) => $q->where('nama_role', 'student'))->count();
        $total_pengajar = User::whereHas('role', fn($q) => $q->where('nama_role', 'teacher'))->count();

        // 2. Siapkan array $stats
        $stats = [
            'total_users'      => User::count(),
            'total_students'   => $total_siswa,
            'total_teachers'   => $total_pengajar,
            
            // GUNAKAN TRY-CATCH atau NILAI MANUAL agar tidak error jika Model belum dibuat
            'total_classes'    => class_exists('App\Models\Classes') ? \App\Models\Classes::count() : 0,
            'pending_payments' => class_exists('App\Models\Payment') ? \App\Models\Payment::where('status', 'pending')->count() : 0,
            'total_revenue'    => class_exists('App\Models\Payment') ? \App\Models\Payment::where('status', 'verified')->sum('total') : 0,
        ];

        // 3. Ambil data pendaftaran terbaru (Pastikan pendaftaranTerbaru ada agar Blade tidak error)
        $pendaftaranTerbaru = User::whereHas('role', fn($q) => $q->where('nama_role', 'student'))
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // 4. Kirim ke view
        return view('admin.dashboard', compact('stats', 'pendaftaranTerbaru'));
    }

    public function galeri()
    {
        return view('admin.galeri.index');
    }

    public function pengumuman()
    {
        return view('admin.pengumuman.index');
    }
}