@extends('layouts.spekta')

@section('title', 'Dashboard Utama')

@section('content')
<div class="space-y-8">
    
    {{-- 1. WELCOME SECTION --}}
    <div class="bg-white p-8 rounded-xl shadow-sm border-l-8 border-[#990000] flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-gray-500 mt-1">Berikut adalah ringkasan perkembangan Spekta Academy hari ini.</p>
        </div>
        <div class="hidden md:block">
            <span class="text-sm font-bold text-[#990000] bg-red-50 px-4 py-2 rounded-full uppercase tracking-widest text-[10px]">
                Admin Spekta
            </span>
        </div>
    </div>

    {{-- 2. STATS GRID (Kini Mengambil Data Real) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1: Total Siswa -->
        <a href="{{ route('admin.siswa.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md hover:-translate-y-1 transition duration-300 group">
            <div class="p-4 bg-blue-50 rounded-lg text-blue-600 mr-4 group-hover:bg-blue-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Siswa</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalStudents ?? 0 }}</p>
            </div>
        </a>

        <!-- Card 2: Pengajar Aktif -->
        <a href="{{ route('admin.pengajar.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md hover:-translate-y-1 transition duration-300 group">
            <div class="p-4 bg-green-50 rounded-lg text-green-600 mr-4 group-hover:bg-green-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pengajar Aktif</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalTeachers ?? 0 }}</p>
            </div>
        </a>

        <!-- Card 3: Menunggu Approval (Pending Payments) -->
        <a href="{{ route('admin.verifikasi.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md hover:-translate-y-1 transition duration-300 group">
            <div class="p-4 bg-yellow-50 rounded-lg text-yellow-600 mr-4 group-hover:bg-yellow-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Menunggu Approval</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingEnrollments ?? 0 }}</p>
            </div>
        </a>

        <!-- Card 4: Total Pendapatan (Sudah Terverifikasi) -->
        <a href="{{ route('admin.pendapatan.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md hover:-translate-y-1 transition duration-300 group">
            <div class="p-4 bg-red-50 text-[#990000] rounded-lg mr-4 group-hover:bg-red-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
        </a>
    </div>

    {{-- 3. MAIN CONTENT: TABEL & PINTASAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: PENDAFTARAN TERBARU -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-50">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-800 uppercase tracking-wider text-sm">Pendaftaran Terbaru</h3>
                <a href="{{ route('admin.siswa.index') }}" class="text-xs text-[#990000] font-bold hover:underline">Lihat Semua Siswa</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase font-bold border-b">
                            <th class="pb-3 px-2">Siswa</th>
                            <th class="pb-3 px-2 text-center">Status Akun</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($pendaftaranTerbaru as $siswa)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-2">
                                <p class="text-sm font-bold text-gray-800 uppercase">{{ $siswa->name }}</p>
                                <p class="text-[10px] text-gray-400 italic">{{ $siswa->email }}</p>
                            </td>
                            <td class="py-4 px-2 text-center">
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border border-blue-100">Aktif</span>
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('admin.siswa.index') }}" class="text-[10px] bg-[#990000] text-white px-4 py-2 rounded-md font-bold uppercase hover:bg-red-800 shadow-sm transition">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-400 text-xs italic">Belum ada pendaftaran akun siswa baru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: PINTASAN & STATUS -->
        <div class="space-y-6">
            {{-- Pintasan Admin --}}
            <div class="bg-[#990000] rounded-xl shadow-lg p-6 text-white">
                <h3 class="font-bold mb-4 uppercase text-[10px] tracking-widest opacity-80">Pintasan Admin</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.jadwal.index') }}" class="flex items-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition border border-white/5 group">
                        <span class="mr-3 text-lg group-hover:scale-110 transition">📅</span>
                        <span class="text-sm font-medium">Update Jadwal Hari Ini</span>
                    </a>
                    <a href="{{ route('admin.promo.index') }}" class="flex items-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition border border-white/5 group">
                        <span class="mr-3 text-lg group-hover:scale-110 transition">🎁</span>
                        <span class="text-sm font-medium">Buat Kode Promo Baru</span>
                    </a>
                    <a href="{{ route('admin.galeri.index') }}" class="flex items-center p-3 bg-white/10 rounded-lg hover:bg-white/20 transition border border-white/5 group">
                        <span class="mr-3 text-lg group-hover:scale-110 transition">🖼️</span>
                        <span class="text-sm font-medium">Upload Foto Galeri</span>
                    </a>
                </div>
            </div>

            {{-- Status Sistem --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 uppercase text-[10px] tracking-widest">Status Sistem</h3>
                <div class="flex items-center text-xs text-green-600 font-bold">
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Server Online & Stabil
                </div>
                <p class="text-[10px] text-gray-400 mt-3 border-t pt-2 italic">
                    Terakhir diperbarui: {{ date('d M Y, H:i') }}
                </p>
            </div>
        </div>

    </div>
</div>
@endsection