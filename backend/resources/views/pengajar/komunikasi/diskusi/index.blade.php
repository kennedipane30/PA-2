@extends('layouts.spekta')

@section('content')
<div class="p-6 max-w-5xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Forum Diskusi</h1>
        <p class="text-gray-500 text-sm">Jawab pertanyaan dari siswa mengenai materi pembelajaran.</p>
    </div>

    <div class="space-y-6">
        <!-- Item Diskusi 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500">A</div>
                    <div>
                        <h4 class="font-bold text-gray-800">Andini Putri <span class="text-xs font-normal text-gray-400 ml-2">2 jam yang lalu</span></h4>
                        <p class="text-xs text-spekta font-semibold">Materi: Logika Matematika</p>
                    </div>
                </div>
                <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase">Belum Dibalas</span>
            </div>
            <p class="text-gray-700 leading-relaxed mb-4">
                "Pak Guru, saya masih kurang paham di bagian penarikan kesimpulan Modus Tollens. Apakah ada contoh lain yang lebih sederhana?"
            </p>
            
            <!-- Form Balas -->
            <form action="#" method="POST" class="mt-4 pt-4 border-t border-gray-50">
                <textarea class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-spekta" rows="2" placeholder="Tulis jawaban Anda..."></textarea>
                <div class="flex justify-end mt-2">
                    <button class="bg-spekta text-white px-5 py-2 rounded-lg text-xs font-bold shadow-md">Kirim Jawaban</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection