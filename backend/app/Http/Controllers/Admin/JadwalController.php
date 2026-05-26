<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\User;
use App\Models\Material;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        // Ambil data program, guru (role_id 2), dan jadwal terbaru dengan relasinya
        $programs = CourseClass::all();
        $teachers = User::where('role_id', 2)->get();

        // Pastikan di Model Jadwal sudah ada relasi 'program' dan 'teacher'
        $jadwal = Jadwal::with(['program', 'teacher'])->latest()->get();

        return view('admin.jadwal.index', compact('programs', 'teachers', 'jadwal'));
    }

    // Fungsi untuk AJAX Materi (Dipanggil saat dropdown Program berubah)
    public function getMaterials($class_id)
    {
        $materials = Material::where('class_id', $class_id)->get();
        return response()->json($materials);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'class_id'       => 'required|exists:course_classes,id', // Sesuaikan nama tabel program Anda
            'user_id'        => 'required|exists:users,user_id',
            'material_title' => 'required|string',
            'date'           => 'required|date',
            'start_time'     => 'required',
            'end_time'       => 'required|after:start_time',
        ], [
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'class_id.required' => 'Program harus dipilih.',
            'user_id.required' => 'Guru harus dipilih.',
        ]);

        // 2. Simpan ke Database
        try {
            Jadwal::create([
                'class_id'       => $request->class_id,
                'user_id'        => $request->user_id,
                'material_title' => $request->material_title,
                'date'           => $request->date,
                'start_time'     => $request->start_time,
                'end_time'       => $request->end_time,
                'status'         => 'aktif', // Opsional jika ada kolom status
            ]);

            return back()->with('success', 'Jadwal belajar berhasil diterbitkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}
