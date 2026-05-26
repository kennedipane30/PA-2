@extends('layouts.app') 

@section('title', 'Manajemen Program Kelas')

@section('content')
<div class="w-full space-y-6">
    <!-- HEADER -->
    <div class="flex justify-between items-center px-2">
        <div>
            <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter text-spekta">Manajemen Program</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Specta Academy Control Panel</p>
        </div>
        
        @if(session('success'))
            <div class="bg-green-600 text-white px-6 py-3 rounded-2xl shadow-lg flex items-center animate-bounce">
                <i class="fa-solid fa-circle-check mr-2"></i>
                <span class="text-xs font-black uppercase tracking-wider">{{ session('success') }}</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: FORM TAMBAH -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                <div class="bg-[#990000] p-5 text-white flex items-center">
                    <i class="fa-solid fa-plus-circle mr-3"></i>
                    <h2 class="font-bold text-xs uppercase tracking-[0.2em]">Tambah Program Baru</h2>
                </div>
                
                <form action="{{ route('admin.program.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    
                    <!-- Nama Program -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Nama Program Kelas</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: CALON ABDI NEGARA" class="w-full border-gray-200 border p-4 rounded-2xl text-sm focus:ring-2 focus:ring-red-500 transition outline-none" required>
                    </div>

                    <!-- BAGIAN FOTO BANNER DENGAN PREVIEW -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Foto Banner Program</label>
                        <div class="relative w-full border-2 border-dashed border-gray-200 rounded-3xl overflow-hidden bg-gray-50 hover:border-red-500 transition group h-48 flex items-center justify-center">
                            <!-- Image Preview Area -->
                            <img id="previewImage" class="absolute inset-0 w-full h-full object-cover hidden z-10">
                            
                            <!-- Placeholder Text & Icon -->
                            <div id="placeholder" class="text-center p-4">
                                <i class="fa-solid fa-images text-gray-300 text-4xl mb-2 group-hover:text-red-500 transition"></i>
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest group-hover:text-red-500">Klik untuk Pilih Foto</p>
                                <p class="text-[8px] text-gray-300 mt-1 uppercase italic">Format: JPG, PNG (Max 2MB)</p>
                            </div>

                            <input type="file" name="image" id="imageInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="previewFile()" required>
                        </div>
                        <!-- Tombol Reset Foto (Muncul jika ada foto) -->
                        <button type="button" id="resetBtn" onclick="resetPreview()" class="mt-2 text-[9px] font-black text-red-700 uppercase hidden">× Batalkan Foto</button>
                    </div>

                    <!-- Harga -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Harga Jual (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-4 text-gray-300 font-bold">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" placeholder="850000" class="w-full border-gray-200 border p-4 pl-12 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-red-500" required>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Deskripsi Singkat</label>
                        <textarea name="description" rows="3" class="w-full border-gray-200 border p-4 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-red-500" placeholder="Jelaskan fasilitas kelas secara singkat..." required>{{ old('description') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#990000] hover:bg-red-800 text-white font-black py-5 rounded-2xl transition duration-300 shadow-xl shadow-red-100 uppercase text-xs tracking-[0.4em]">
                        SIMPAN
                    </button>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: DAFTAR TABEL -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden min-h-[600px]">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-black text-gray-500 uppercase tracking-widest text-[10px]">Daftar Program Aktif</h3>
                    <span class="bg-red-50 text-red-700 px-3 py-1 rounded-full text-[9px] font-black border border-red-100 uppercase italic tracking-wider">{{ $programs->count() }} Terbit</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase text-gray-300 font-black tracking-widest border-b border-gray-50">
                                <th class="p-6">Banner & Nama Kelas</th>
                                <th class="p-6 text-center">Harga</th>
                                <th class="p-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($programs as $p)
                            <tr class="hover:bg-gray-50/50 transition group">
                                <td class="p-6 flex items-center space-x-5">
                                    <div class="w-24 h-16 rounded-2xl overflow-hidden bg-gray-100 shadow-sm border border-gray-200 flex-shrink-0">
                                        @if($p->image)
                                            <img src="{{ url('storage/' . $p->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-50 italic text-[9px] text-gray-300 font-bold">No Image</div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-800 uppercase tracking-tight text-sm group-hover:text-red-700 transition">
                                            {{ $p->title }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1 line-clamp-1 italic font-medium">{{ $p->description }}</p>
                                    </div>
                                </td>
                                <td class="p-6 text-center">
                                    <span class="text-sm font-black text-gray-800 tracking-tighter">
                                        Rp {{ number_format($p->price, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="p-6 text-center">
                                    <form action="{{ url('admin/program/delete/' . $p->getKey()) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 bg-white border border-red-50 text-red-500 rounded-xl hover:bg-red-600 hover:text-white transition shadow-sm flex items-center justify-center mx-auto">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-32 text-center text-gray-200">
                                    <i class="fa-solid fa-layer-group text-7xl mb-4"></i>
                                    <p class="font-black text-[10px] uppercase tracking-[0.3em]">Belum Ada Data</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT LIVE PREVIEW -->
<script>
    function previewFile() {
        const preview = document.getElementById('previewImage');
        const file = document.getElementById('imageInput').files[0];
        const placeholder = document.getElementById('placeholder');
        const resetBtn = document.getElementById('resetBtn');
        const reader = new FileReader();

        reader.addEventListener("load", function () {
            // Tampilkan Gambar yang dipilih
            preview.src = reader.result;
            preview.classList.remove('hidden');
            // Sembunyikan Placeholder
            placeholder.classList.add('hidden');
            // Tampilkan tombol batal
            resetBtn.classList.remove('hidden');
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    function resetPreview() {
        const preview = document.getElementById('previewImage');
        const input = document.getElementById('imageInput');
        const placeholder = document.getElementById('placeholder');
        const resetBtn = document.getElementById('resetBtn');

        input.value = "";
        preview.src = "";
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        resetBtn.classList.add('hidden');
    }
</script>
@endsection