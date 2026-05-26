@extends('layouts.spekta')

@section('title', 'Manajemen Kode Promo')

@section('content')
<div class="space-y-6">
    
    {{-- TAMPILAN NOTIFIKASI --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
            <p class="text-sm font-bold">✅ Sukses: {{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
            <p class="text-sm font-bold">❌ Kesalahan Input:</p>
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD UTAMA --}}
    <div class="bg-white rounded-xl shadow-md border-t-4 border-[#990000] p-6">
        <div class="flex items-center mb-6">
            <div class="w-1 h-6 bg-[#990000] mr-3 rounded-full"></div>
            <h2 class="text-xl font-bold text-[#990000] uppercase tracking-wider">Manajemen Kode Promo</h2>
        </div>
        
        {{-- FORM INPUT PROMO BARU --}}
        <form action="{{ route('admin.promo.store') }}" method="POST" class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                {{-- Input Kode --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Kode Promo</label>
                    <input type="text" name="kode_promo" placeholder="CONTOH: SPEKTA50" class="w-full border border-gray-300 rounded-md p-2 uppercase focus:ring-1 focus:ring-[#990000] outline-none text-sm shadow-sm" required value="{{ old('kode_promo') }}">
                </div>

                {{-- Pilih Target Kelas (Disesuaikan dengan Database Anda) --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Target Kelas</label>
                    <select name="class_id" class="w-full border border-gray-300 rounded-md p-2 text-sm bg-white focus:ring-1 focus:ring-[#990000] outline-none shadow-sm cursor-pointer">
                        <option value="">🌍 Berlaku Global (Semua Kelas)</option>
                        @foreach($classes as $c)
                            {{-- Menggunakan $c->id dan $c->title sesuai ClassModel Anda --}}
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>
                                🎓 {{ $c->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Diskon & Tipe --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Besar Diskon</label>
                    <div class="flex shadow-sm">
                        <input type="number" name="diskon" placeholder="Nilai" class="w-full border border-gray-300 rounded-l-md p-2 text-sm focus:ring-1 focus:ring-[#990000] outline-none" required value="{{ old('diskon') }}">
                        <select name="tipe_diskon" class="border-y border-r border-gray-300 rounded-r-md p-2 bg-gray-100 text-xs font-bold focus:outline-none">
                            <option value="percentage" {{ old('tipe_diskon') == 'percentage' ? 'selected' : '' }}>%</option>
                            <option value="fixed" {{ old('tipe_diskon') == 'fixed' ? 'selected' : '' }}>Rp</option>
                        </select>
                    </div>
                </div>

                {{-- Kuota --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Kuota (Jml Pemakai)</label>
                    <input type="number" name="kuota" placeholder="Contoh: 100" class="w-full border border-gray-300 rounded-md p-2 text-sm shadow-sm focus:ring-1 focus:ring-[#990000] outline-none" required value="{{ old('kuota') }}">
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full border border-gray-300 rounded-md p-2 text-sm shadow-sm focus:ring-1 focus:ring-[#990000] outline-none" required value="{{ old('start_date', date('Y-m-d')) }}">
                </div>

                {{-- Tanggal Expired --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-widest">Tanggal Berakhir</label>
                    <input type="date" name="expired" class="w-full border border-gray-300 rounded-md p-2 text-sm shadow-sm focus:ring-1 focus:ring-[#990000] outline-none" required value="{{ old('expired') }}">
                </div>

                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="w-full bg-[#990000] text-white font-bold py-2.5 rounded-md hover:bg-red-800 transition shadow-md uppercase tracking-tighter text-sm">
                        🚀 TERBITKAN KODE PROMO
                    </button>
                </div>
            </div>
        </form>

        {{-- TABEL DAFTAR PROMO --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-200">
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Kode</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Berlaku Untuk</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest">Potongan</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-600 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($promos as $promo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 font-bold text-[#990000] text-sm uppercase">{{ $promo->kode_promo }}</td>
                        
                        <td class="px-4 py-4">
                            @if($promo->class_id)
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-blue-100 shadow-sm">
                                    {{-- Menggunakan title sesuai ClassModel --}}
                                    {{ $promo->classModel->title ?? 'Program Dipilih' }}
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase italic border border-gray-200 shadow-sm">
                                    🌐 Global (Semua)
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-sm font-black text-gray-800">
                            {{ $promo->tipe_diskon == 'percentage' ? $promo->diskon.'%' : 'Rp '.number_format($promo->diskon, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-4 text-center">
                            <form action="{{ route('admin.promo.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] border border-gray-300 px-4 py-1.5 rounded uppercase font-black text-gray-600 hover:bg-red-600 hover:text-white transition duration-200">
                                    HAPUS
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 italic">Belum ada promo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection