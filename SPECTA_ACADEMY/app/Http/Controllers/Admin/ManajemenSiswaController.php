<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;

class ManajemenSiswaController extends Controller
{
    /**
     * 1. Menampilkan Daftar Semua Siswa Terdaftar (General CRUD)
     */
    public function index()
    {
        // Ambil semua user yang rolenya siswa (ID: 3)
        $siswas = User::where('role_id', 3)->with('student')->latest()->get();
        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * 2. Menampilkan Daftar Antrean Pendaftaran (Pending Verification)
     * Ini fungsi utama untuk melihat siapa yang sudah bayar tapi belum masuk kelas.
     */
    public function pendaftaranKelas()
    {
        // Ambil data dari tabel enrollments yang statusnya PENDING
        $pendaftar = Enrollment::with(['user.student', 'classModel'])
                                ->where('status', 'pending')
                                ->latest()
                                ->get();

        return view('admin.siswa.pendaftaran', compact('pendaftar'));
    }

    /**
     * 3. Form Aktivasi (Halaman untuk input durasi akses)
     * Menampilkan data lengkap: Nama, NISN, Nama Ortu, Alamat, dan BUKTI BAYAR.
     */
    public function formAktivasi($enrollmentsID)
    {
        $enroll = Enrollment::with(['user.student', 'classModel'])->findOrFail($enrollmentsID);
        return view('admin.siswa.aktivasi_form', compact('enroll'));
    }

    /**
     * 4. Proses Memasukkan Siswa ke Kelas (Activation)
     * Mengubah status jadi Aktif dan menentukan masa berlaku (Expires At).
     */
    public function aktivasiSiswa(Request $request, $enrollmentsID)
    {
        // Validasi: Admin wajib memasukkan angka durasi (Mata Kuliah: Kualitas/Security)
        $request->validate([
            'durasi' => 'required|numeric|min:1',
        ], [
            'durasi.required' => 'Tentukan berapa hari siswa dapat mengakses kelas!'
        ]);

        $enroll = Enrollment::findOrFail($enrollmentsID);

        // Update Status & Set Tanggal Kadaluarsa otomatis (Mata Kuliah: Aplikasi Terdistribusi)
        $enroll->update([
            'status' => 'aktif',
            'expires_at' => now()->addDays($request->durasi)
        ]);

        // Berikan notifikasi sukses ke Admin
        return redirect()->route('admin.siswa.pendaftaran')
                         ->with('success', 'Siswa ' . $enroll->user->name . ' berhasil diaktifkan selama ' . $request->durasi . ' hari.');
    }
}
