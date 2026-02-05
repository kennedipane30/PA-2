<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistik untuk dashboard (Syarat Matkul Proyek II)
        $total_siswa = User::where('role_id', 3)->count();
        $total_pengajar = User::where('role_id', 2)->count();

        return view('admin.dashboard', compact('total_siswa', 'total_pengajar'));
    }

    public function galeri()
    {
        return view('admin.galeri'); // Pastikan file resources/views/admin/galeri.blade.php ada
    }

    public function pengumuman()
    {
        return view('admin.pengumuman');
    }
}
