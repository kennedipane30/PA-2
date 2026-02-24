@extends('layouts.spekta')
@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold mb-2">Daftar Absensi: {{ $class->nama_program }}</h3>
    <p class="text-sm text-gray-500 mb-6">Siswa yang muncul adalah mereka yang pendaftarannya sudah diverifikasi Admin.</p>

    <table class="w-full text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4">Nama Siswa</th>
                <th class="p-4">NISN</th>
                <th class="p-4">Sisa Masa Akses</th>
                <th class="p-4">Status Absen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $s)
            <tr class="border-b hover:bg-red-50">
                <td class="p-4 font-bold">{{ $s->user->name }}</td>
                <td class="p-4">{{ $s->user->student->nisn }}</td>
                <td class="p-4 text-red-600 font-bold">
                    {{ now()->diffInDays($s->expires_at) }} Hari Lagi
                </td>
                <td class="p-4">
                    <select class="border rounded p-1 text-xs">
                        <option>HADIR</option>
                        <option>IZIN</option>
                        <option>ALPA</option>
                    </select>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
