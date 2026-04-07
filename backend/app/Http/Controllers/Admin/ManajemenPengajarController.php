<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManajemenPengajarController extends Controller
{
    public function index()
    {
        // Menggunakan scopeTeachers yang sudah Anda buat di model User
        $teachers = User::teachers()->latest()->get();
        return view('admin.pengajar.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat User (untuk Login)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => 2, // ID untuk pengajar
                'is_verified' => 1, // Langsung verifikasi agar bisa login
            ]);

            // 2. Buat data detail di tabel teachers
            // Sesuaikan dengan kolom di tabel teachers Anda
            Teacher::create([
                'user_id' => $user->user_id, // Menggunakan user_id sesuai model User Anda
                'specialization' => 'Umum'
            ]);

            DB::commit();
            return back()->with('success', 'Akun Pengajar berhasil didaftarkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // $id di sini akan berisi user_id dari route
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun Pengajar berhasil dihapus!');
    }
}
