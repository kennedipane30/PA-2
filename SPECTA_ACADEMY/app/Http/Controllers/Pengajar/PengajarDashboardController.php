<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajarDashboardController extends Controller
{
    public function index() {
        return view('pengajar.dashboard');
    }

    public function absensi() {
        return view('pengajar.absensi'); // Centang kehadiran siswa per kelas
    }
}
