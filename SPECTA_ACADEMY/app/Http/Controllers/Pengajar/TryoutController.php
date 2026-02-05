<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TryoutController extends Controller
{
    public function buatSoal() {
        return view('pengajar.tryout.create'); // Form input pertanyaan & kunci jawaban
    }

    public function lihatNilai() {
        return view('pengajar.tryout.nilai'); // Melihat skor yang didapat siswa dari mobile
    }
}
