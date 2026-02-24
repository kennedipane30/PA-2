@extends('layouts.spekta')
@section('title', 'Absensi: ' . $class->nama_program)

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-bold uppercase">{{ $class->nama_program }}</h3>
            <p class="text-sm text-gray-500 italic">Daftar siswa yang memiliki akses aktif di kelas ini.</p>
        </div>
        <div class="text-right">
            <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-xs font-bold uppercase">
                Total: {{ $siswas->count() }} Siswa
            </span>
        </div>
    </div>

    <table class="w-full text-left border-collapse mt-4">
        <thead>
            <tr class="bg-gray-50 text-xs font-bold uppercase text-gray-600">
                <th class="p-4 border-b text-center">No</th>
                <th class="p-4 border-b">Nama Siswa</th>
                <th class="p-4 border-b">NISN</th>
                <th class="p-4 border-b">Sisa Masa Akses</th>
                <th class="p-4 border-b">Aksi Absensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswas as $index => $row)
            <tr class="hover:bg-red-50 transition">
                <td class="p-4 border-b text-center text-gray-400">{{ $index + 1 }}</td>
                <td class="p-4 border-b font-bold">{{ $row->user->name }}</td>
                <td class="p-4 border-b text-sm text-gray-600">{{ $row->user->student->nisn ?? '-' }}</td>
                <td class="p-4 border-b">
                    <span class="text-red-700 font-bold bg-red-50 px-2 py-1 rounded">
                        {{ now()->diffInDays($row->expires_at) }} Hari Lagi
                    </span>
                </td>
                <td class="p-4 border-b">
                    <div class="flex gap-2">
                        <button class="bg-green-500 text-white px-3 py-1 rounded text-[10px] font-bold">HADIR</button>
                        <button class="bg-yellow-500 text-white px-3 py-1 rounded text-[10px] font-bold">IZIN</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded text-[10px] font-bold">ALPA</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-20 text-center text-gray-400 italic font-medium">
                    Belum ada siswa yang aktif di kelas ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
