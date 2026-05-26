@extends('layouts.spekta')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Periksa Tugas Siswa</h1>
        <p class="text-gray-500 text-sm">Berikan nilai dan feedback pada tugas yang telah dikumpulkan.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 bg-yellow-50 border-b border-yellow-100">
            <p class="text-yellow-700 text-sm font-medium">Menunggu Diperiksa: <b>5 Tugas</b></p>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Nama Siswa</th>
                    <th class="px-6 py-4">Tugas</th>
                    <th class="px-6 py-4">Tanggal Kirim</th>
                    <th class="px-6 py-4">File</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold">Budi Santoso</td>
                    <td class="px-6 py-4">Latihan Aljabar Bab 1</td>
                    <td class="px-6 py-4 text-gray-500">20 Mar 2024, 14:00</td>
                    <td class="px-6 py-4">
                        <a href="#" class="text-spekta font-bold flex items-center gap-1">
                            <span>📄</span> Download
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="alert('Buka Modal Penilaian')" class="bg-gray-800 text-white px-4 py-2 rounded shadow-sm hover:bg-black">
                            Beri Nilai
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection