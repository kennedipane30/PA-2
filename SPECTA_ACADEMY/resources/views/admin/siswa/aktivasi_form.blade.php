@extends('layouts.spekta')
@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-lg border-t-8 border-[#990000]">
    <h2 class="text-2xl font-bold mb-6 text-spekta">VERIFIKASI SISWA</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- DATA LENGKAP SISWA -->
        <div class="space-y-4">
            <h4 class="font-bold border-b pb-2">Identitas Siswa</h4>
            <p class="text-sm"><b>Nama:</b> {{ $enroll->user->name }}</p>
            <p class="text-sm"><b>NISN:</b> {{ $enroll->user->student->nisn }}</p>
            <p class="text-sm"><b>Gmail:</b> {{ $enroll->user->email }}</p>
            <p class="text-sm"><b>Alamat:</b> {{ $enroll->user->student->school }}</p>
            <p class="text-sm"><b>Nama Ortu:</b> {{ $enroll->user->student->parent_name }}</p>

            <h4 class="font-bold border-b pb-2 mt-6">Program Dipilih</h4>
            <p class="text-xl font-bold text-red-700 uppercase">{{ $enroll->classModel->nama_program }}</p>
        </div>

        <!-- BUKTI TRANSFER -->
        <div>
            <h4 class="font-bold border-b pb-2 mb-4">Bukti Pembayaran</h4>
            <img src="{{ asset('storage/'.$enroll->payment_proof) }}" class="w-full rounded-lg shadow border" alt="Bukti Transfer">

            <!-- FORM AKTIVASI -->
            <form action="{{ route('admin.pendaftaran.aktivasi', $enroll->enrollmentsID) }}" method="POST" class="mt-8 bg-gray-50 p-4 rounded-xl">
                @csrf
                <label class="block text-sm font-bold mb-2">Set Lama Akses (Hari)</label>
                <input type="number" name="durasi" value="30" class="w-full border p-2 rounded mb-4" required>
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-bold">TAMBAHKAN KE KELAS</button>
            </form>
        </div>
    </div>
</div>
@endsection
