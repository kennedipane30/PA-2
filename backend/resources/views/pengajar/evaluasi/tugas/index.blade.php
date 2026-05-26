@extends('layouts.spekta')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Tugas</h1>
            <p class="text-gray-500 text-sm">Kelola daftar tugas dan deadline untuk siswa Anda.</p>
        </div>
        <button class="bg-spekta text-white px-5 py-2 rounded-lg font-bold hover:bg-red-700 transition">
            + Tambah Tugas Baru
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Nama Tugas</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Deadline</th>
                    <th class="px-6 py-4">Terkumpul</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <!-- Contoh Data Dummy -->
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">Latihan Aljabar Bab 1</td>
                    <td class="px-6 py-4 text-gray-600">Intensif UTBK A</td>
                    <td class="px-6 py-4 text-red-500 font-medium">25 Mar 2024, 23:59</td>
                    <td class="px-6 py-4">12 / 20 Siswa</td>
                    <td class="px-6 py-4 text-center">
                        <button class="text-blue-500 hover:underline mx-2">Edit</button>
                        <button class="text-red-500 hover:underline mx-2">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection