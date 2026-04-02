<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
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
    }
}
