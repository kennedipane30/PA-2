@extends('layouts.spekta')
@section('title', 'Manajemen Galeri')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-[#990000]">
    <h2 class="text-xl font-bold text-[#990000] mb-4">Upload Foto Baru</h2>
    <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="flex gap-4">
        @csrf
        <input type="text" name="judul" placeholder="Judul Foto..." class="border p-2 rounded w-full">
        <input type="file" name="foto" class="border p-2 rounded">
        <button type="submit" class="bg-[#990000] text-white px-6 py-2 rounded font-bold">SIMPAN</button>
    </form>

    <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($galeri as $row)
        <div class="border rounded-lg p-2 shadow-sm">
            <img src="{{ asset('storage/'.$row->foto) }}" class="w-full h-32 object-cover rounded">
            <p class="mt-2 font-bold text-sm">{{ $row->judul }}</p>
            <p class="text-[10px] text-gray-400">Diunggah: {{ $row->created_at->diffForHumans() }}</p>

            <!-- Tombol Hapus (CRUD) -->
            <form action="{{ route('admin.galeri.destroy', $row->id) }}" method="POST" class="mt-2">
                @csrf @method('DELETE')
                <button class="text-red-500 text-xs font-bold">HAPUS</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
