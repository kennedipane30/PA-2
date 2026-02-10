<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // 1. Tambahkan import Carbon untuk urusan waktu

class GaleriController extends Controller
{
    /**
     * Tampilan untuk Web Admin (CRUD)
     * Admin bisa melihat SEMUA data tanpa batasan waktu
     */
    public function index() {
        $galeri = Gallery::latest()->get();
        return view('admin.galeri.index', compact('galeri'));
    }

    /**
     * Tampilan untuk API Mobile (Siswa)
     * HANYA menampilkan foto yang diunggah dalam 14 hari terakhir
     */
    public function apiIndex()
    {
        // 2. Logika Filter: Ambil data yang created_at >= (Hari ini minus 14 hari)
        $batasWaktu = now()->subDays(14);

        $galeri = Gallery::where('created_at', '>=', $batasWaktu)
                        ->latest()
                        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $galeri
        ]);
    }

    /**
     * Fungsi Simpan Foto (Admin)
     */
    public function store(Request $request) {
        $request->validate([
            'judul' => 'required',
            'foto' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $path = $request->file('foto')->store('galeri', 'public');

        Gallery::create([
            'judul' => $request->judul,
            'foto' => $path,
            'deskripsi' => $request->deskripsi
        ]);

        return back()->with('success', 'Foto berhasil diunggah ke Galeri!');
    }

    /**
     * Fungsi Hapus Foto (Admin)
     */
    public function destroy($id) {
        $item = Gallery::findOrFail($id);
        Storage::disk('public')->delete($item->foto);
        $item->delete();
        return back()->with('success', 'Foto berhasil dihapus!');
    }
}
