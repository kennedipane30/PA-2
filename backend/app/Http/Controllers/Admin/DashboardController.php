<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment; // <--- SEKARANG AKTIFKAN INI

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Siswa & Pengajar (Role ID: 3=Siswa, 2=Pengajar)
        $totalStudents = User::where('role_id', 3)->count();
        $totalTeachers = User::where('role_id', 2)->count();
        
        // 2. Ambil 5 Siswa yang baru saja mendaftar akun
        $pendaftaranTerbaru = User::where('role_id', 3)
                                ->latest('user_id')
                                ->take(5)
                                ->get();

        // 3. DATA REAL PEMBAYARAN (Berdasarkan tabel payments yang kita buat tadi)
        
        // Menunggu Approval: Hitung pembayaran yang statusnya masih 'pending'
        $pendingEnrollments = Payment::where('status', 'pending')->count(); 

        // Total Pendapatan: Jumlahkan kolom 'total_bayar' yang statusnya sudah 'verified' (Lunas)
        $totalRevenue = Payment::where('status', 'verified')->sum('total_bayar');

        // 4. KIRIM DATA KE VIEW
        return view('admin.dashboard', [
            'totalStudents'      => $totalStudents,
            'totalTeachers'      => $totalTeachers,
            'pendaftaranTerbaru' => $pendaftaranTerbaru,
            'pendingEnrollments' => $pendingEnrollments,
            'totalRevenue'       => $totalRevenue,
        ]);
    }
}