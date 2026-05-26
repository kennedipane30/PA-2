@extends('layouts.spekta') {{-- Menggunakan layout spekta agar muncul sidebar merah --}}

@section('title', 'Daftar Siswa')

@section('content')
{{-- Container Utama --}}
<div class="bg-white rounded-xl shadow-md border-t-4 border-[#990000] p-6">
    
    {{-- Header Halaman --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-[#990000] uppercase tracking-wider">Daftar Siswa Terverifikasi</h2>
            <p class="text-sm text-gray-500 mt-1">List siswa yang sudah aktif mengikuti program Spekta Academy.</p>
        </div>
        
        {{-- Pencarian --}}
        <div class="relative">
            <input type="text" placeholder="Cari siswa..." 
                class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#990000] w-64">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b-2 border-gray-100">
                    <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Gmail</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Program</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($siswas as $key => $siswa)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $key + 1 }}</td>
                    
                    {{-- PERBAIKAN: Langsung panggil $siswa->name --}}
                    <td class="px-4 py-4 text-sm font-semibold text-gray-800 uppercase">
                        {{ $siswa->name }}
                    </td>
                    
                    {{-- PERBAIKAN: Langsung panggil $siswa->email --}}
                    <td class="px-4 py-4 text-sm text-gray-600 italic">
                        {{ $siswa->email ?? '-' }}
                    </td>

                    <td class="px-4 py-4 text-sm text-gray-600">
                        {{-- Logika untuk menampilkan program atau pesan default --}}
                        @if($siswa->enrollments && $siswa->enrollments->first())
                            <span class="text-[#990000] font-medium uppercase">
                                {{ $siswa->enrollments->first()->program->nama_program ?? 'Program Dihapus' }}
                            </span>
                        @else
                            <span class="text-red-700 font-bold italic text-xs">BELUM PILIH PROGRAM</span>
                        @endif
                    </td>

                    <td class="px-4 py-4 text-sm">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                            Aktif
                        </span>
                    </td>

                    <td class="px-4 py-4 text-sm text-center">
                        <div class="flex justify-center">
                            {{-- Tombol Edit --}}
                            <a href="#" class="border border-gray-400 text-gray-700 px-4 py-1 rounded shadow-sm hover:bg-gray-100 transition text-xs font-bold uppercase">
                                EDIT
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 italic">
                        <div class="flex flex-col items-center">
                            <span class="text-3xl mb-2">📂</span>
                            <p>Belum ada siswa yang mendaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection