<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    // 1. Tampil Daftar Galeri
    public function index()
    {
        $galleries = Gallery::latest()->get();
        return view('admin.galeri.index', compact('galleries'));
    }

    // 2. Simpan Foto Baru (Create)
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('galeri', 'public');
        }

        Gallery::create([
            'judul'     => $request->judul,
            'foto'      => $path,
            'deskripsi' => $request->deskripsi,
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan!');
    }

    // 3. Form Edit (INI YANG TADI KURANG/ERROR)
    public function edit($id)
    {
        $gallery = Gallery::findOrFail($id);
        return view('admin.galeri.edit', compact('gallery'));
    }

    // 4. Proses Update Data
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $gallery = Gallery::findOrFail($id);
        $data = [
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada foto baru yang diunggah
            if ($gallery->foto) {
                Storage::disk('public')->delete($gallery->foto);
            }
            $data['foto'] = $request->file('foto')->store('galeri', 'public');
        }

        $gallery->update($data);

        return redirect()->route('admin.galeri.index')->with('success', 'Galeri berhasil diperbarui!');
    }

    // 5. Hapus Foto
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        
        if ($gallery->foto) {
            Storage::disk('public')->delete($gallery->foto);
        }
        
        $gallery->delete();

        return back()->with('success', 'Foto berhasil dihapus!');
    }
}