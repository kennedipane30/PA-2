<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spekta Academy - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-spekta { background-color: #990000; }
        .text-spekta { color: #990000; }
        .border-spekta { border-color: #990000; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 bg-spekta text-white flex-shrink-0 shadow-lg">
            <div class="p-6 border-b border-red-800 flex flex-col items-center">
                <h1 class="text-2xl font-bold tracking-widest">SPEKTA</h1>
                <p class="text-xs italic">ACADEMY</p>
            </div>
            <nav class="mt-4 overflow-y-auto h-full pb-20">
                @if(Auth::user()->role_id == 1) <!-- MENU ADMIN -->
                    <div class="px-6 py-2 text-xs uppercase text-red-300">Menu Administrasi</div>
                    <a href="/admin/dashboard" class="block py-3 px-6 hover:bg-red-800">🏠 Dashboard</a>
                    <a href="/admin/siswa" class="block py-3 px-6 hover:bg-red-800">👥 Manajemen Siswa</a>
                    <a href="/admin/pembayaran" class="block py-3 px-6 hover:bg-red-800">💳 Pembayaran</a>
                    <a href="/admin/promo" class="block py-3 px-6 hover:bg-red-800">🎁 Kode Promo</a>
                    <a href="/admin/galeri" class="block py-3 px-6 hover:bg-red-800">🖼️ Galeri & Info</a>
                @elseif(Auth::user()->role_id == 2) <!-- MENU PENGAJAR -->
                    <div class="px-6 py-2 text-xs uppercase text-red-300">Menu Pengajar</div>
                    <a href="/pengajar/dashboard" class="block py-3 px-6 hover:bg-red-800">🏠 Dashboard</a>
                    <a href="/pengajar/absensi" class="block py-3 px-6 hover:bg-red-800">📝 Absensi Siswa</a>
                    <a href="/pengajar/materi" class="block py-3 px-6 hover:bg-red-800">📚 Upload Materi</a>
                    <a href="/pengajar/soal-tryout" class="block py-3 px-6 hover:bg-red-800">⏱️ Input Soal TO</a>
                    <a href="/pengajar/nilai" class="block py-3 px-6 hover:bg-red-800">📊 Lihat Nilai</a>
                @endif
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow px-8 py-4 flex justify-between items-center">
                <h2 class="font-bold text-gray-700">@yield('title')</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-bold">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-spekta">{{ Auth::user()->role->nama_role }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="bg-spekta text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:bg-red-700">LOGOUT</button>
                    </form>
                </div>
            </header>
            <main class="p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
