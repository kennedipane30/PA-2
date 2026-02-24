<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class PengajarDashboardController extends Controller
{
    /**
     * Dashboard Utama Pengajar
     */
    public function index()
    {
        return view('pengajar.dashboard');
    }

    /**
     * Menampilkan Daftar 4 Kelas (Redirect atau tampilan cadangan)
     */
    public function absensi()
    {
        $classes = ClassModel::all();
        return view('pengajar.absensi.index', compact('classes'));
    }

    /**
     * Menampilkan Daftar Siswa di Kelas Tertentu (Dinamis)
     * Hanya siswa yang AKTIF dan BELUM EXPIRED
     */
    public function showAbsensi($class_id)
    {
        // Pastikan model dipanggil dengan benar
        $class = ClassModel::findOrFail($class_id);

        // Filter: Status AKTIF dan Waktu Sekarang belum melewati Masa Berlaku (Matkul: Keamanan & Integrity)
        $siswas = Enrollment::where('class_id', $class_id)
                    ->where('status', 'aktif')
                    ->where('expires_at', '>', now())
                    ->with(['user.student']) // Eager loading profil siswa
                    ->get();

        return view('pengajar.absensi.show', compact('siswas', 'class'));
    }
}
