<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Student; // Pastikan model Student di-import
use Illuminate\Http\Request;

class SiswaAdminController extends Controller 
{
    /**
     * Menampilkan semua siswa dan status kelengkapan profilnya
     */
    public function semuaSiswa() 
    {
        // Kita muat relasi 'student' untuk mengecek data Nama Ortu, Alamat, dll
        $siswas = User::where('role_id', 3)
                    ->with('student') 
                    ->latest('user_id')
                    ->get();

        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * Menampilkan siswa yang menunggu verifikasi pendaftaran
     */
    public function daftarTunggu() 
    {
        $enrollments = Enrollment::with(['user', 'program'])
                        ->where('status', 'pending')
                        ->get();

        return view('admin.siswa.daftar_tunggu', compact('enrollments'));
    }

    /**
     * Verifikasi pendaftaran siswa (Enrollment)
     */
    public function verifikasi($id) 
    {
        $enroll = Enrollment::find($id);
        if ($enroll) {
            $enroll->update(['status' => 'approved']);
            return redirect()->back()->with('success', 'Pendaftaran siswa berhasil diverifikasi!');
        }
        
        return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
    }

    /**
     * Helper: Fungsi untuk Admin membantu melengkapi profil siswa jika error
     */
    public function updateProfilManual(Request $request, $user_id)
    {
        $student = Student::updateOrCreate(
            ['user_id' => $user_id],
            [
                'nama_ortu' => $request->nama_ortu,
                'alamat'    => $request->alamat,
                'wa_ortu'   => $request->wa_ortu,
            ]
        );

        return redirect()->back()->with('success', 'Profil siswa berhasil diperbarui secara manual.');
    }
}