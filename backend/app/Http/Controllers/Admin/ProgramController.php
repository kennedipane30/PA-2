<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass; // Pastikan model ini mengarah ke tabel 'programs'
use Illuminate\Http\Request;

class ProgramController extends Controller {
    public function index() {
        $programs = CourseClass::all();
        return view('admin.siswa.tambah_kelas', compact('programs'));
    }

    public function store(Request $request) {
    $request->validate([
        'title' => 'required',
        'price' => 'required|numeric',
    ]);

    CourseClass::create([
        'title' => $request->title,
        'description' => $request->description,
        'price' => $request->price,
        'harga_promo' => $request->harga_promo ?? 0,
        'is_promo' => $request->has('is_promo'), // Centang jika ingin promo aktif
        'pesan_promo' => $request->pesan_promo,
    ]);

    return redirect()->back()->with('success', 'Program dan Promo berhasil disimpan!');
}

    public function destroy($id) {
        CourseClass::destroy($id);
        return redirect()->back()->with('success', 'Program Kelas berhasil dihapus!');
    }
}