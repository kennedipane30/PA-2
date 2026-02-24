<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ClassModel; // Wajib import untuk menampilkan daftar kelas
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
     * Menampilkan Daftar 4 Kelas (Pilihan untuk di-absen)
     */
    public function absensi()
    {
        // Ambil 4 program utama dari database
        $classes = ClassModel::all();
        return view('pengajar.absensi.index', compact('classes'));
    }

    /**
     * Menampilkan Daftar Siswa di Kelas Tertentu (Dinamis)
     * Hanya siswa yang AKTIF dan BELUM EXPIRED
     */
    public function showAbsensi($class_id)
    {
        $class = ClassModel::findOrFail($class_id);

        // Logic Filter: Status Aktif & Masa Berlaku masih ada
        $siswas = Enrollment::where('class_id', $class_id)
                            ->where('status', 'aktif')
                            ->where('expires_at', '>', now()) // Syarat Matakuliah: Akses Terbatas Waktu
                            ->with(['user.student']) // Eager Load data user & student
                            ->get();

        return view('pengajar.absensi.show', compact('siswas', 'class'));
    }
}
