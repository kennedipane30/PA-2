@extends('layouts.spekta')
@section('title', 'Dashboard Pengajar')

@section('content')
<div class="space-y-6">
    <!-- Header Welcome -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border-t-8 border-spekta transition-all hover:shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, Bapak/Ibu Guru!</h1>
                <p class="text-gray-500 mt-1 italic">"Mencerdaskan bangsa bersama Spekta Academy"</p>
            </div>
            <div class="flex gap-2 text-sm font-medium">
                <span class="bg-red-50 text-spekta px-4 py-2 rounded-lg border border-red-100">
                    Semester Ganjil 2024
                </span>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik (Point 6: Personal Analytics) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm">Total Siswa Aktif</p>
            <h3 class="text-2xl font-bold text-gray-800">156</h3>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-spekta">
            <p class="text-gray-500 text-sm">Tugas Perlu Dinilai</p>
            <h3 class="text-2xl font-bold italic">8 Baru</h3>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm">Materi Terbit</p>
            <h3 class="text-2xl font-bold text-gray-800">12 File</h3>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-gray-500 text-sm">Rata-rata Nilai Tryout</p>
            <h3 class="text-2xl font-bold text-green-600">82.5</h3>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri (Manajemen Konten & Tugas) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- List Tugas Masuk (Point 2: Assessment) -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden text-sm">
                <div class="p-4 border-b border-gray-50 flex justify-between items-center">
                    <h4 class="font-bold text-gray-800">Tugas Masuk (Menunggu Penilaian)</h4>
                    <a href="#" class="text-spekta text-xs font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="p-0">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-50">
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="p-4 font-medium">Andi Wijaya</td>
                                <td class="p-4">Latihan Matematika Bab 3</td>
                                <td class="p-4 text-gray-400">1 jam yang lalu</td>
                                <td class="p-4 text-right">
                                    <button class="bg-spekta text-white px-3 py-1 rounded text-xs">Beri Nilai</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="p-4 font-medium">Siti Rahma</td>
                                <td class="p-4">Tugas Esai Logika</td>
                                <td class="p-4 text-gray-400">3 jam yang lalu</td>
                                <td class="p-4 text-right">
                                    <button class="bg-spekta text-white px-3 py-1 rounded text-xs">Beri Nilai</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Manajemen Materi Singkat (Point 1: Materi & Kurikulum) -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-sm">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-bold text-gray-800">Update Materi Terakhir</h4>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded-lg text-xs hover:bg-black transition-all">
                        + Materi Baru
                    </button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📄</span>
                            <div>
                                <p class="font-bold">Modul SBMPTN 2024.pdf</p>
                                <p class="text-xs text-gray-500 italic">Dilihat oleh 45 Siswa</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-spekta">Update</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (Informasi & Aksi Cepat) -->
        <div class="space-y-6 text-sm">
            
            <!-- Quick Actions (Absensi & Live) -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4 text-sm">
                <h4 class="font-bold text-gray-800 mb-2">Aksi Cepat</h4>
                
                <a href="/pengajar/absensi" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-red-50 group transition-all">
                    <span class="font-medium group-hover:text-spekta">📅 Absensi Siswa Hari Ini</span>
                    <span class="text-gray-400">→</span>
                </a>

                <div class="p-4 bg-spekta rounded-xl text-white">
                    <h5 class="font-bold mb-1 italic">Live Class Mendatang</h5>
                    <p class="text-xs opacity-90 mb-3 leading-tight">Matematika Dasar - 14:00 WIB</p>
                    <a href="#" class="block text-center bg-white text-spekta py-2 rounded font-bold text-xs hover:bg-gray-100 transition-all">
                        Buka Zoom Class
                    </a>
                </div>
            </div>

            <!-- Pengumuman (Point 4: Interaksi) -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm text-sm">
                <h4 class="font-bold text-gray-800 mb-3">Kirim Pengumuman</h4>
                <textarea class="w-full p-3 border border-gray-200 rounded-lg text-xs" rows="3" placeholder="Tulis pesan untuk siswa..."></textarea>
                <button class="w-full mt-2 bg-gray-800 text-white py-2 rounded font-bold text-xs">Kirim Notifikasi</button>
            </div>

        </div>
    </div>
</div>
@endsection