<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
=======
use App\Models\ClassModel;
use App\Models\Material;
use Illuminate\Http\Request;
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
<<<<<<< HEAD
    // Menampilkan halaman daftar materi
    public function index()
    {
        $materis = Materi::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('pengajar.materi.index', compact('materis'));
    }

    // Menangani upload materi
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,mp4,mov,avi|max:50000', // Max 50MB
        ]);

        // Simpan file ke folder storage/app/public/materi
        $file = $request->file('file');
        $path = $file->store('materi', 'public');

        // Simpan data ke database
        Materi::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'tipe' => $file->getClientOriginalExtension() == 'pdf' ? 'pdf' : 'video',
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Materi berhasil diunggah!');
=======
    public function index()
{
    // 1. Dapatkan ID pengajar yang sedang login
    $teacherId = \Illuminate\Support\Facades\Auth::id();

    // 2. Ambil ID Program/Kelas yang hanya diajar oleh guru ini (dari tabel schedules)
    $assignedClassIds = \App\Models\Schedule::where('teacher_id', $teacherId)
                        ->pluck('class_id')
                        ->unique() // Agar tidak ada ID yang duplikat
                        ->toArray();

    // 3. Dropdown pilihan program hanya menampilkan kelas yang diajar guru ini
    $classes = \App\Models\ClassModel::whereIn('id', $assignedClassIds)->get();

    // 4. Tabel history hanya menampilkan materi untuk kelas yang diajar guru ini
    $materials = \App\Models\Material::whereIn('class_id', $assignedClassIds)
                ->with('classModel')
                ->orderBy('class_id')
                ->orderBy('order_priority', 'asc')
                ->get();

    return view('pengajar.materi.index', compact('classes', 'materials'));
}

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'title' => 'required', // Subjek: TIU, TWK, dll
            'order_priority' => 'required|numeric',
            'file_pdf' => 'required|mimes:pdf|max:10240', // Maksimal 10MB
        ]);

        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');

            // Nama file unik: materi_MTK_urutan1_timestamp.pdf
            $filename = 'materi_' . $request->title . '_ke' . $request->order_priority . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Simpan ke storage/app/public/materi
            $path = $file->storeAs('materi', $filename, 'public');

            Material::create([
                'class_id' => $request->class_id,
                'title' => $request->title,
                'order_priority' => $request->order_priority,
                'file_path' => $path,
            ]);

            return back()->with('success', 'Materi berhasil diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        // Hapus file dari storage
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
    }

    // Menghapus materi
    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);
        Storage::disk('public')->delete($materi->file_path);
        $materi->delete();

        return redirect()->back()->with('success', 'Materi berhasil dihapus!');
    }
}