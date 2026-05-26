<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller {
    
    /**
     * Tampilan utama Manajemen Kelas di Web Admin
     */
    public function index() {
        $programs = CourseClass::all();
        return view('admin.siswa.tambah_kelas', compact('programs'));
    }

    /**
     * Menyimpan Program Kelas Baru dari Web Admin
     */
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Gambar disimpan di storage/app/public/programs
            $imagePath = $request->file('image')->store('programs', 'public');
        }

        // Simpan ke database
        // Laravel akan otomatis menggunakan 'program_id' jika sudah diatur di Model
        CourseClass::create([
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $imagePath, 
        ]);

        return redirect()->back()->with('success', 'Program Kelas Berhasil Disimpan!');
    }

    /**
     * Mengupdate Data Program Kelas
     */
    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
        ]);

        // findOrFail akan mencari berdasarkan primary key 'program_id'
        $program = CourseClass::findOrFail($id);
        
        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada sebelum ganti yang baru
            if ($program->image) {
                Storage::disk('public')->delete($program->image);
            }
            $data['image'] = $request->file('image')->store('programs', 'public');
        }

        $program->update($data);

        return redirect()->route('admin.program.index')->with('success', 'Program Berhasil Diperbarui!');
    }

    /**
     * Menghapus Program Kelas dari Web Admin
     */
    public function destroy($id) {
        $program = CourseClass::findOrFail($id);
        
        // Hapus file gambar di storage agar tidak memenuhi memori
        if ($program->image) {
            Storage::disk('public')->delete($program->image);
        }

        $program->delete();

        return redirect()->back()->with('success', 'Program Berhasil Dihapus!');
    }

    // =========================================================================
    // --- API UNTUK APLIKASI FLUTTER ---
    // =========================================================================

    /**
     * Ambil semua daftar kelas (Untuk halaman Pilih Program di Mobile)
     */
    public function getApi() {
        try {
            $programs = CourseClass::all()->map(function($item) {
                // Memberikan URL lengkap agar gambar bisa muncul di HP
                $item->image_url = $item->image ? asset('storage/' . $item->image) : null;
                return $item;
            });
            return response()->json(['success' => true, 'data' => $programs], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil satu detail kelas berdasarkan ID (Untuk halaman Pendaftaran/Pembayaran)
     */
    public function showApi($id) {
        try {
            $program = CourseClass::find($id);
            if (!$program) {
                return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
            }

            $program->image_url = $program->image ? asset('storage/' . $program->image) : null;

            return response()->json(['success' => true, 'data' => $program], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat detail kelas'], 500);
        }
    }
}