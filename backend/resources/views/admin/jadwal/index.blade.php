@extends('layouts.spekta')
@section('title', 'Kelola Jadwal Belajar')

@section('content')
<div class="bg-white p-8 rounded-2xl shadow-md border-t-8 border-[#990000]">
    <h3 class="text-xl font-bold mb-6 text-spekta uppercase">Kelola Jadwal Belajar</h3>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Notifikasi Error Validasi -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM INPUT JADWAL -->
    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="bg-gray-50 p-8 rounded-2xl mb-10 border border-gray-100">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Pilih Program -->
            <div>
                <label class="block text-xs font-bold mb-2 uppercase text-gray-600">Pilih Program Kelas</label>
                <select name="class_id" id="program_select" class="w-full border-gray-300 p-3 rounded-xl focus:ring-spekta focus:border-spekta" required>
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ old('class_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Guru -->
            <div>
                <label class="block text-xs font-bold mb-2 uppercase text-gray-600">Pilih Guru Pengajar</label>
                <select name="user_id" class="w-full border-gray-300 p-3 rounded-xl focus:ring-spekta focus:border-spekta" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->user_id }}" {{ old('user_id') == $t->user_id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Materi (Dinamis via JS) -->
            <div>
                <label class="block text-xs font-bold mb-2 uppercase text-gray-600">Materi Pelajaran</label>
                <select name="material_title" id="material_select" class="w-full border-gray-300 p-3 rounded-xl bg-gray-100 cursor-not-allowed" required disabled>
                    <option value="">Pilih Program Dahulu</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Tanggal -->
            <div>
                <label class="block text-xs font-bold mb-2 uppercase text-gray-600">Tanggal Belajar</label>
                <input type="date" name="date" value="{{ old('date') }}" class="w-full border-gray-300 p-3 rounded-xl focus:ring-spekta focus:border-spekta" required>
            </div>

            <!-- Jam Mulai -->
            <div>
                <label class="block text-xs font-bold mb-2 uppercase text-gray-600">Jam Mulai</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full border-gray-300 p-3 rounded-xl focus:ring-spekta focus:border-spekta" required>
            </div>

            <!-- Jam Selesai -->
            <div>
                <label class="block text-xs font-bold mb-2 uppercase text-gray-600">Jam Selesai</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full border-gray-300 p-3 rounded-xl focus:ring-spekta focus:border-spekta" required>
            </div>
        </div>

        <button type="submit" class="w-full bg-spekta hover:bg-red-800 text-white font-bold py-4 mt-8 rounded-2xl shadow-lg transition duration-300 uppercase tracking-wider">
            🚀 Terbitkan Jadwal Belajar
        </button>
    </form>

    <!-- TABEL DAFTAR JADWAL -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 text-xs font-bold uppercase text-gray-700">
                <tr>
                    <th class="p-4 border-b">Waktu & Tanggal</th>
                    <th class="p-4 border-b">Program</th>
                    <th class="p-4 border-b">Materi & Guru</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($jadwal as $j)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4">
                        <div class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($j->date)->translatedFormat('d M Y') }}</div>
                        <div class="text-xs text-gray-500 font-medium">🕒 {{ $j->start_time }} - {{ $j->end_time }} WIB</div>
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 bg-red-100 text-spekta rounded-full text-xs font-bold uppercase">
                            {{ $j->program->title ?? 'Program Tidak Ditemukan' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-gray-800">{{ $j->material_title }}</div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">👨‍🏫 {{ $j->teacher->name ?? 'Guru Tidak Ditemukan' }}</div>
                    </td>
                    <td class="p-4 text-center">
                        <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-tighter">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-400 italic font-medium">Belum ada jadwal belajar yang diterbitkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- JAVASCRIPT UNTUK LOADING MATERI OTOMATIS -->
<script>
document.getElementById('program_select').addEventListener('change', function() {
    const classId = this.value;
    const materialSelect = document.getElementById('material_select');

    // Reset Dropdown Materi saat loading
    materialSelect.innerHTML = '<option value="">Memuat Materi...</option>';
    materialSelect.disabled = true;
    materialSelect.classList.add('bg-gray-100', 'cursor-not-allowed');

    if (classId) {
        // Panggil route AJAX yang sudah kita buat di web.php
        fetch(`/admin/jadwal/get-materials/${classId}`)
            .then(response => response.json())
            .then(data => {
                materialSelect.innerHTML = '<option value="">-- Pilih Materi --</option>';

                if(data.length > 0) {
                    data.forEach(materi => {
                        const option = document.createElement('option');
                        option.value = materi.title; // Simpan Judul Materi
                        option.textContent = materi.title;
                        materialSelect.appendChild(option);
                    });
                    materialSelect.disabled = false;
                    materialSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                } else {
                    materialSelect.innerHTML = '<option value="">Tidak ada materi di program ini</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                materialSelect.innerHTML = '<option value="">Gagal memuat materi</option>';
            });
    } else {
        materialSelect.innerHTML = '<option value="">Pilih Program Dahulu</option>';
        materialSelect.disabled = true;
        materialSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
    }
});
</script>
@endsection
