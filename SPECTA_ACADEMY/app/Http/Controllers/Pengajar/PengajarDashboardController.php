<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    /**
     * Dashboard Utama Pengajar
     */
    public function index(): View
    {
        return view('pengajar.dashboard');
    }

    /**
     * Menampilkan daftar 4 kelas untuk di-absen
     */
    public function absensi(): View
    {
        $classes = ClassModel::all();
        return view('pengajar.absensi.index', compact('classes'));
    }

    /**
     * Menampilkan Daftar Siswa di Kelas Tertentu
     */
    public function showAbsensi($class_id): View
    {
        $class = ClassModel::findOrFail($class_id);

        $siswas = Enrollment::where('class_id', $class_id)
                    ->where('status', 'aktif')
                    ->where('expires_at', '>', now())
                    ->with(['user.student'])
                    ->get();

        return view('pengajar.absensi.show', compact('siswas', 'class'));
    }

    /**
     * Menampilkan Jadwal Mengajar milik pengajar yang sedang login
     * (Hanya ada SATU fungsi di sini agar tidak error)
     */
    public function jadwalSaya(): View
    {
        $jadwal = Schedule::where('teacher_id', Auth::id())
                    ->with('classModel')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal.index', compact('jadwal'));
    }
}
