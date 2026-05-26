<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiskusiController extends Controller
{
    // Menampilkan daftar pertanyaan dari siswa
    public function index()
    {
        return view('pengajar.komunikasi.diskusi.index');
    }

    // Membalas pertanyaan siswa
    public function reply(Request $request)
    {
        $request->validate([
            'diskusi_id' => 'required',
            'pesan' => 'required|string',
        ]);

        // Logika simpan balasan ke database
        // DiskusiReply::create([...]);

        return redirect()->back()->with('success', 'Balasan berhasil terkirim!');
    }
}