<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenSiswaController extends Controller
{
    public function index() {
        $siswas = User::where('role_id', 3)->with('profile')->get();
        return view('admin.siswa.index', compact('siswas'));
    }

    public function kelolaKelas() {
        return view('admin.siswa.kelas'); // Manajemen daftar kelas (SD, SMP, SMA)
    }

    public function daftarkanSiswa(Request $request) {
        // Logic untuk memasukkan siswa ke kelas tertentu (Enrollment)
        return back()->with('success', 'Siswa berhasil dimasukkan ke kelas');
    }
}
