@extends('layouts.spekta')
@section('title', 'Daftar Tunggu Pendaftaran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Tunggu Pendaftaran</h1>
    <p class="text-gray-500 text-sm">Siswa di bawah ini telah mendaftar melalui aplikasi mobile dan menunggu verifikasi Anda.</p>
</div>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-[11px] font-bold uppercase text-gray-600">
            <tr>
                <th class="p-4 border-b text-center">Tgl Daftar</th>
                <th class="p-4 border-b">Nama Siswa</th>
                <th class="p-4 border-b">Gmail</th>
                <th class="p-4 border-b text-center">Program Dipilih</th>
                <th class="p-4 border-b text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @forelse($enrollments as $e)
            <tr class="hover:bg-gray-50 border-b">
                <td class="p-4 text-center text-gray-500">{{ $e->created_at->format('d/m/Y') }}</td>
                <td class="p-4 font-medium text-gray-800">{{ $e->user->name }}</td>
                <td class="p-4 text-gray-600">{{ $e->user->email }}</td>
                <td class="p-4 text-center">
                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase">
                        {{ $e->program->title ?? 'N/A' }}
                    </span>
                </td>
                <td class="p-4 text-center">
                    <a href="{{ route('admin.siswa.verifikasi', $e->enrollment_id) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition shadow-sm"
                       onclick="return confirm('Apakah Anda yakin ingin menyetujui pendaftaran ini?')">
                        TERIMA & AKTIFKAN
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-12 text-center text-gray-400 italic">
                    <div class="flex flex-col items-center">
                        <span class="text-4xl mb-2">📥</span>
                        <span>Tidak ada siswa yang menunggu pendaftaran kelas.</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 