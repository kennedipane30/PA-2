<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;    // Import Model Payment
use App\Models\ClassModel; // Import Model Class (Sesuai ERD kamu)
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data dasar (Gunakan 'name' sesuai ERD tabel roles kamu)
        $total_siswa = User::whereHas('role', fn($q) => $q->where('name', 'siswa'))->count();
        $total_pengajar = User::whereHas('role', fn($q) => $q->where('name', 'pengajar'))->count();

        // 2. Siapkan data statistik untuk dashboard
        $stats = [
            'total_users'      => User::count(),
            'total_students'   => $total_siswa,
            'total_teachers'   => $total_pengajar,
            'total_classes'    => ClassModel::count(), // Menggunakan ClassModel sesuai ERD
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_revenue'    => Payment::where('status', 'verified')->sum('total_bayar'),
        ];

        // 3. Ambil data siswa yang baru mendaftar (untuk Log Aktivitas)
        $pendaftaranTerbaru = User::where('role_id', 3) // Role 3 adalah siswa
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // 4. Kirim ke view dashboard
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
