@extends('layouts.spekta')
@section('title', 'Data Siswa Terdaftar')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <div class="flex justify-between mb-6">
        <h3 class="font-bold text-lg">Daftar Siswa</h3>
        <button class="bg-spekta text-white px-4 py-2 rounded-lg text-sm">+ Tambah Siswa</button>
    </div>
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50">
            <tr>
                <th class="p-4 border-b">Nama</th>
                <th class="p-4 border-b">Gmail</th>
                <th class="p-4 border-b">WA Ortu</th>
                <th class="p-4 border-b">Status</th>
                <th class="p-4 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $siswa)
            <tr class="hover:bg-gray-50">
                <td class="p-4 border-b">{{ $siswa->name }}</td>
                <td class="p-4 border-b">{{ $siswa->email }}</td>
                <td class="p-4 border-b text-sm">{{ $siswa->profile->nomor_wa_ortu ?? '-' }}</td>
                <td class="p-4 border-b">
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Aktif</span>
                </td>
                <td class="p-4 border-b">
                    <button class="text-blue-600 font-bold text-xs mr-2">Edit</button>
                    <button class="text-red-600 font-bold text-xs">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
