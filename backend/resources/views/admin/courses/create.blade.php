<!-- resources/views/admin/courses/create.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas - Specta Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #990000; border: none; }
        .btn-primary:hover { background-color: #770000; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-3">
                <a href="{{ route('admin.courses.index') }}" class="text-decoration-none text-muted">← Kembali ke Daftar</a>
            </div>
            
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold" style="color: #990000;">Tambah Kelas Baru</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.courses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kelas</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Flutter Masterclass" value="{{ old('name') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Harga (Rupiah)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="Contoh: 900000" value="{{ old('price') }}">
                            </div>
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Kelas</label>
                            <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Jelaskan isi materi kelas...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold">SIMPAN KELAS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>