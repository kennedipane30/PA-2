<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    // Menampilkan daftar tugas yang dibuat oleh pengajar
    public function index()
    {
        // Nantinya ambil data dari model Tugas
        // $tugas = Tugas::where('pengajar_id', Auth::id())->get();
        return view('pengajar.evaluasi.tugas.index');
    }

    // Menampilkan daftar jawaban/tugas yang dikumpulkan siswa (perlu diperiksa)
    public function periksa()
    {
        return view('pengajar.evaluasi.tugas.periksa');
    }

    // Proses menyimpan nilai tugas siswa
    public function simpanNilai(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string'
        ]);

        // Logika simpan nilai ke database
        // $submission = TugasSubmission::findOrFail($id);
        // $submission->update([...]);

        return redirect()->back()->with('success', 'Nilai berhasil berikan!');
    }
}