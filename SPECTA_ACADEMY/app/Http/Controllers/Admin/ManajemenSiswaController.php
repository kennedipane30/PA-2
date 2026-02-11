<?php
namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;

class PengajarDashboardController extends Controller {
    public function absensi() {
        // Hanya ambil siswa yang statusnya 'aktif' (sudah diverifikasi admin)
        $siswas = Enrollment::with('user', 'classModel')
                            ->where('status', 'aktif')
                            ->get();
        return view('pengajar.absensi', compact('siswas'));
    }
}
