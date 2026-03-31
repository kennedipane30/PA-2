<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data dasar
        $total_siswa = User::where('role_id', 3)->count();
        $total_pengajar = User::where('role_id', 2)->count();

        // 2. Siapkan array $stats dengan SEMUA key yang dipanggil di View
        $stats = [
            'total_users'      => User::count(),
            'total_students'   => $total_siswa,
            'total_teachers'   => $total_pengajar,
            'total_classes'    => 0, // Sementara isi 0 agar tidak error
            'pending_payments' => 0, // Sementara isi 0
            'total_revenue'    => 0, // TAMBAHKAN INI untuk memperbaiki error baris 84
        ];

        // 3. Kirim ke view (Jangan lupa kirim total_siswa karena baris 34 di view memanggilnya langsung)
        return view('admin.dashboard', compact('total_siswa', 'stats'));
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