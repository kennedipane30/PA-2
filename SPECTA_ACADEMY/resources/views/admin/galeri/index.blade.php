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
    .btn-outline-spekta { border: 1px solid var(--spekta-red); color: var(--spekta-red); }
    .btn-outline-spekta:hover { background-color: var(--spekta-red); color: white; }
    
    .card { border-radius: 10px; overflow: hidden; }
    .card-header { font-weight: 600; letter-spacing: 0.5px; }
    .gallery-img { transition: transform 0.3s ease; }
    .gallery-img:hover { transform: scale(1.05); }
</style>

<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <div style="width: 5px; height: 35px; background: var(--spekta-red); margin-right: 15px;"></div>
        <h2 class="mb-0 text-spekta">Manajemen Galeri Spekta Academy</h2>
    </div>

    <!-- Form Tambah -->
    <div class="card shadow border-0 mb-5">
        <div class="card-header bg-spekta">
            <i class="fas fa-plus-circle me-2"></i>Tambah Foto Kegiatan Baru
        </div>
        <div class="card-body bg-light">
            <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Judul Kegiatan</label>
                        <input type="text" name="judul" class="form-control border-0 shadow-sm" placeholder="Contoh: Tryout Akbar 2024" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Pilih Foto</label>
                        <input type="file" name="foto" class="form-control border-0 shadow-sm" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control border-0 shadow-sm" rows="1" placeholder="Opsional..."></textarea>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-spekta px-4 shadow-sm">
                        <i class="fas fa-upload me-2"></i>Unggah Ke Galeri
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid Daftar Foto -->
    <div class="row">
        @foreach($galleries as $g)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100 shadow border-0">
                <!-- Wrapper Foto untuk Efek Hover -->
                <div style="overflow: hidden;">
                    <img src="{{ asset('storage/' . $g->foto) }}" class="card-img-top gallery-img" alt="foto" style="height: 200px; object-fit: cover;">
                </div>
                
                <div class="card-body">
                    <h5 class="card-title fw-bold" style="font-size: 1.1rem; color: #333;">{{ $g->judul }}</h5>
                    <p class="card-text text-muted small" style="height: 40px; overflow: hidden;">
                        {{ $g->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>
                
                <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                    <!-- Tombol Edit -->
                    <a href="{{ route('admin.galeri.edit', $g->id) }}" class="btn btn-sm btn-outline-warning w-100 fw-bold">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    <!-- Tombol Hapus -->
                    <form action="{{ route('admin.galeri.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')" class="w-100">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-spekta w-100 fw-bold">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($galleries->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-image fa-4x text-muted mb-3"></i>
        <p class="text-muted">Belum ada foto di galeri.</p>
    </div>
    @endif
</div>
@endsection