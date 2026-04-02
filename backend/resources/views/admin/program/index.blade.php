@extends('layouts.spekta')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Program Kelas</h1>
        <button onclick="toggleModal()" class="bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow-lg hover:bg-red-800">
            + BUAT KELAS BARU
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold">
                <tr>
                    <th class="p-4 border-b">Nama Program</th>
                    <th class="p-4 border-b">Harga</th>
                    <th class="p-4 border-b text-center">Mata Pelajaran</th>
                    <th class="p-4 border-b text-center">Status Promo</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($programs as $p)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4 font-bold text-red-900">{{ $p->title }}</td>
                    <td class="p-4">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $p->materials_count }} Materi
                        </span>
                        <a href="{{ route('admin.program.show', $p->id) }}" class="ml-2 text-blue-600 hover:underline text-xs">Kelola Isi</a>
                    </td>
                    <td class="p-4 text-center">
                        @if($p->is_promo)
                            <span class="text-green-600 font-bold">Aktif</span>
                        @else
                            <span class="text-gray-400">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <button class="text-yellow-600 font-bold mr-2">Edit</button>
                        <button class="text-red-600 font-bold">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection