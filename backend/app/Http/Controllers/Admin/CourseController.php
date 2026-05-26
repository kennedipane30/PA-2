<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Tampilkan Daftar Kelas
    public function index() {
        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    // Tampilkan Form Tambah
    public function create() {
        return view('admin.courses.create');
    }

    // Simpan data ke Database
    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);

        Course::create($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Kelas berhasil ditambah!');
    }

    // Hapus Kelas
    public function destroy($id) {
        Course::destroy($id);
        return redirect()->back()->with('success', 'Kelas berhasil dihapus!');
    }
}