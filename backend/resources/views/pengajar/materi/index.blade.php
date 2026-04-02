@extends('layouts.spekta')

@section('title', 'Manajemen Materi PDF')

@section('content')
<div class="container mx-auto">
    <!-- FORM UPLOAD MATERI -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-10 border-t-4 border-red-700">
        <div class="flex items-center mb-6">
            <div class="bg-red-100 p-3 rounded-full mr-4">
                <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">Unggah Materi Baru</h3>
        </div>

        <!-- Notifikasi Berhasil -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex justify-between items-center shadow-sm">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-900 font-bold">&times;</button>
            </div>
        @endif

        <form action="{{ route('pengajar.materi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Dropdown Program Kelas -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">1. Pilih Program Kelas</label>
                    <select name="class_id" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-red-500 transition bg-gray-50">
                        <option value="">-- Pilih Program --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Subjek Materi -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">2. Pilih Mata Pelajaran (Subjek)</label>
                    <select name="title" required class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-red-500 transition bg-gray-50">
                        <option value="">-- Pilih Subjek --</option>
                        <option value="TIU">TIU (Tes Intelegensia Umum)</option>
                        <option value="TWK">TWK (Tes Wawasan Kebangsaan)</option>
                        <option value="Psikotes">Psikotes</option>
                        <option value="Bahasa Inggris">Bahasa Inggris</option>
                        <option value="Matematika">Matematika</option>
                        <option value="Fisika">Fisika</option>
                        <option value="Kimia">Kimia</option>
                        <option value="Biologi">Biologi</option>
                    </select>
                </div>

                <!-- Urutan Materi -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">3. Materi Ke-berapa? (Urutan)</label>
                    <input type="number" name="order_priority" required placeholder="Contoh: 1" class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-red-500 transition bg-gray-50">
                    <p class="text-[10px] text-gray-500 mt-1 italic">* Urutan ini akan digunakan siswa di aplikasi mobile.</p>
                </div>

                <!-- Input File PDF -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">4. File Materi (PDF Saja)</label>
                    <input type="file" name="file_pdf" accept=".pdf" required class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition">
                </div>
            </div>

            <button type="submit" class="w-full md:w-auto bg-red-700 hover:bg-red-800 text-white font-bold py-4 px-12 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                SIMPAN DAN UPLOAD MATERI
            </button>
        </form>
    </div>

    <!-- TABEL DAFTAR MATERI TERUPLOAD -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700 uppercase tracking-wider">📋 Riwayat Materi Terunggah</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 border-b">Program Kelas</th>
                        <th class="px-6 py-4 border-b">Mata Pelajaran</th>
                        <th class="px-6 py-4 border-b text-center">Materi Ke-</th>
                        <th class="px-6 py-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($materials as $m)
                    <tr class="hover:bg-red-50/30 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $m->classModel->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-red-700 text-white rounded-full text-[10px] font-bold uppercase">
                                {{ $m->title }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mx-auto font-bold text-gray-700">
                                {{ $m->order_priority }}
                            </div>
                        </td>
                        <td class="px-6 py-4 flex items-center space-x-4">
                            <!-- Link Lihat PDF -->
                            <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center font-bold">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                LIHAT
                            </a>

                            <!-- Tombol Hapus -->
                            @if($m->materialsID)
                                <form action="{{ route('pengajar.materi.destroy', ['materi' => $m->materialsID]) }}" method="POST" onsubmit="return confirm('Hapus materi ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 flex items-center font-bold">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        HAPUS
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                            📭 Belum ada materi yang diunggah untuk saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
