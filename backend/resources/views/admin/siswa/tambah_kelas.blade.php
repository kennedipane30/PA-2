@extends('layouts.spekta') 

@section('content')
<div class="container mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Program Kelas & Promo</h1>
        <p class="text-gray-500 text-sm">Gunakan form ini untuk menambah kelas dan mengatur harga promo yang muncul di HP siswa.</p>
    </div>

    <!-- FORM TAMBAH -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 border-t-4 border-red-700">
        <h2 class="text-lg font-bold mb-4 text-red-700 uppercase">Tambah Program Baru</h2>
        <form action="{{ route('admin.program.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Nama Program -->
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Program Kelas</label>
                    <input type="text" name="title" placeholder="Contoh: CALON ABDI NEGARA" class="w-full border p-2 rounded-lg focus:ring-red-500 focus:border-red-500" required>
                </div>

                <!-- Harga Normal -->
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Normal (Rp)</label>
                    <input type="number" name="price" placeholder="900000" class="w-full border p-2 rounded-lg" required>
                </div>

                <!-- Harga Promo -->
                <div class="col-span-1 bg-red-50 p-2 rounded-lg">
                    <label class="block text-sm font-bold text-red-700 mb-2 italic">Harga Promo (Rp) *</label>
                    <input type="number" name="harga_promo" placeholder="Isi jika ada diskon" class="w-full border-red-300 border p-2 rounded-lg focus:ring-red-500">
                    <p class="text-[10px] text-red-500 mt-1">* Kosongkan jika harga normal.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan fasilitas kelas di sini..." class="w-full border p-2 rounded-lg" required></textarea>
                </div>

                <!-- Status Promo -->
                <div class="flex items-center mt-8 space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_promo" value="1" class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label class="ml-2 text-sm font-bold text-gray-700">AKTIFKAN STATUS PROMO</label>
                    </div>
                    <input type="text" name="pesan_promo" placeholder="Teks Promo (Contoh: Diskon Lebaran)" class="border p-2 rounded-lg text-sm flex-1">
                </div>
            </div>

            <div class="mt-6 text-right">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-8 rounded-full transition duration-300 shadow-lg">
                    SIMPAN & PUBLIKASIKAN KE APLIKASI
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR KELAS (Daftar yang sudah dibuat) -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="p-4 border-b">Program</th>
                    <th class="p-4 border-b text-center">Harga Asli</th>
                    <th class="p-4 border-b text-center">Harga Promo</th>
                    <th class="p-4 border-b text-center">Status</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $p)
                <tr class="text-sm hover:bg-gray-50 border-b">
                    <td class="p-4 font-bold text-red-800">{{ $p->title }}</td>
                    <td class="p-4 text-center text-gray-500">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td class="p-4 text-center font-bold text-red-600">
                        {{ $p->harga_promo > 0 ? 'Rp ' . number_format($p->harga_promo, 0, ',', '.') : '-' }}
                    </td>
                    <td class="p-4 text-center">
                        @if($p->is_promo)
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-[10px] font-bold uppercase">PROMO AKTIF</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-[10px] font-bold uppercase">NORMAL</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <form action="{{ route('admin.program.delete', $p->id) }}" method="POST" onsubmit="return confirm('Hapus program ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs font-bold">HAPUS</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection