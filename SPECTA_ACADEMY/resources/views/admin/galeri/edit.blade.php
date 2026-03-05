@extends('layouts.app')

@section('content')
<style>
    /* Custom Spekta Red Theme */
    :root {
        --spekta-red: #b30000;
        --spekta-red-hover: #8b0000;
    }

    .text-spekta { color: var(--spekta-red); font-weight: bold; }
    .bg-spekta { background-color: var(--spekta-red) !important; color: white !important; }
    .btn-spekta { background-color: var(--spekta-red); color: white; border: none; transition: 0.3s; }
    .btn-spekta:hover { background-color: var(--spekta-red-hover); color: white; }
    
    .card { border-radius: 12px; border: none; }
    .card-header { font-weight: 600; letter-spacing: 0.5px; border-radius: 12px 12px 0 0 !important; }
    .form-label { font-weight: 600; color: #444; }
    .img-preview { border: 3px solid #eee; border-radius: 8px; max-width: 100%; height: auto; margin-bottom: 10px; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header Title -->
            <div class="d-flex align-items-center mb-4">
                <div style="width: 5px; height: 30px; background: var(--spekta-red); margin-right: 15px;"></div>
                <h3 class="mb-0 text-spekta">Edit Galeri Kegiatan</h3>
            </div>

            <div class="card shadow-lg">
                <div class="card-header bg-spekta py-3">
                    <i class="fas fa-edit me-2"></i> Perbarui Data Foto
                </div>
                <div class="card-body p-4 bg-light">
                    <form action="{{ route('admin.galeri.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Judul -->
                        <div class="mb-4">
                            <label class="form-label">Judul Kegiatan</label>
                            <input type="text" name="judul" class="form-control border-0 shadow-sm" value="{{ $gallery->judul }}" placeholder="Masukkan judul kegiatan..." required>
                        </div>

                        <!-- Foto Saat Ini -->
                        <div class="mb-4">
                            <label class="form-label d-block">Foto Saat Ini</label>
                            <div class="text-center bg-white p-3 rounded shadow-sm mb-2">
                                <img src="{{ asset('storage/' . $gallery->foto) }}" class="img-preview shadow-sm" alt="Preview Foto">
                                <p class="text-muted small mt-2">Foto yang sedang digunakan</p>
                            </div>
                            <label class="form-label mt-2">Ganti Foto (Opsional)</label>
                            <input type="file" name="foto" class="form-control border-0 shadow-sm">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG (Max. 2MB).</div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label class="form-label">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi" class="form-control border-0 shadow-sm" rows="4" placeholder="Tuliskan deskripsi singkat mengenai foto ini...">{{ $gallery->deskripsi }}</textarea>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between align-items-center mt-5 border-top pt-4">
                            <a href="{{ route('admin.galeri.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-spekta px-5 shadow">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection