@extends('layouts.spekta') {{-- Menggunakan layout spekta yang ada sidebarnya --}}

@section('title', 'Manajemen Galeri')

@section('content')
<div class="space-y-8">
    
    {{-- BAGIAN ATAS: FORM TAMBAH FOTO --}}
    <div class="bg-white rounded-xl shadow-md border-t-4 border-[#990000] p-6">
        <div class="flex items-center mb-6">
            <div class="w-1 h-6 bg-[#990000] mr-3 rounded-full"></div>
            <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wider">Tambah Foto Kegiatan Baru</h2>
        </div>

        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Judul --}}
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-600 mb-2 uppercase">Judul Kegiatan</label>
                    <input type="text" name="judul" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#990000] transition" placeholder="Contoh: Tryout Akbar 2024" required>
                </div>

                {{-- Pilih Foto --}}
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-600 mb-2 uppercase">Unggah Foto</label>
                    <input type="file" name="foto" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer" required>
                </div>

                {{-- Deskripsi --}}
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-600 mb-2 uppercase">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="1" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#990000] transition" placeholder="Opsional..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-[#990000] text-white text-xs font-bold py-3 px-8 rounded-lg hover:bg-red-800 shadow-md transition-all uppercase tracking-widest">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Unggah Ke Galeri
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- BAGIAN BAWAH: GRID DAFTAR FOTO --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($galleries as $g)
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow group">
            {{-- Foto --}}
            <div class="relative overflow-hidden h-48 bg-gray-200">
                <img src="{{ asset('storage/' . $g->foto) }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" alt="foto">
                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            {{-- Info Konten --}}
            <div class="p-4">
                <h5 class="font-bold text-gray-800 text-sm truncate uppercase tracking-tight">{{ $g->judul }}</h5>
                <p class="text-xs text-gray-500 mt-1 line-clamp-2 h-8 leading-relaxed">
                    {{ $g->deskripsi ?? 'Tidak ada deskripsi.' }}
                </p>
            </div>

            {{-- Tombol Aksi --}}
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex gap-2">
                <a href="{{ route('admin.galeri.edit', $g->id) }}" class="flex-1 text-center border border-yellow-500 text-yellow-600 py-1.5 rounded text-[10px] font-bold hover:bg-yellow-500 hover:text-white transition uppercase">
                    Edit
                </a>

                <form action="{{ route('admin.galeri.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')" class="flex-1">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full border border-[#990000] text-[#990000] py-1.5 rounded text-[10px] font-bold hover:bg-[#990000] hover:text-white transition uppercase">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($galleries->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <p class="italic text-sm">Belum ada koleksi foto di galeri Spekta Academy.</p>
    </div>
    @endif

</div>
@endsection