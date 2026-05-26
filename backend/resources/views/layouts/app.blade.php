<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spekta Academy - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-spekta { background-color: #990000; }
        .active-menu { background-color: #7a0000; border-left: 4px solid #fbbf24; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #990000; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #7a0000; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-72 bg-spekta text-white flex-shrink-0 shadow-xl flex flex-col">
            <div class="p-8 border-b border-red-800 flex flex-col items-center justify-center">
                <h1 class="text-3xl font-black tracking-tighter">SPEKTA</h1>
                <p class="text-[10px] uppercase tracking-[0.3em] opacity-80 -mt-1">Academy</p>
            </div>
            
            <nav class="flex-1 mt-4 overflow-y-auto sidebar-scroll pb-10">
                <div class="px-6 py-2 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center py-3 px-6 hover:bg-red-800 {{ request()->routeIs('admin.dashboard') ? 'active-menu' : '' }}">
                    <i class="fa-solid fa-gauge-high w-6 mr-3 text-center"></i> Dashboard
                </a>

                <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Akademik</div>
                <!-- MENU BARU: MANAJEMEN KELAS -->
                <a href="{{ route('admin.program.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 {{ request()->is('admin/program*') ? 'active-menu' : '' }}">
                    <i class="fa-solid fa-layer-group w-6 mr-3 text-center"></i> Manajemen Kelas
                </a>
                <a href="{{ route('admin.jadwal.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800">
                    <i class="fa-solid fa-calendar-days w-6 mr-3 text-center"></i> Jadwal Kelas
                </a>

                <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Manajemen User</div>
                <div class="relative">
                    <button onclick="toggleDropdown('siswa-menu')" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 focus:outline-none">
                        <span class="flex items-center"><i class="fa-solid fa-user-graduate w-6 mr-3 text-center"></i> Siswa</span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    <div id="siswa-menu" class="hidden bg-black bg-opacity-10 py-2">
                        <a href="{{ route('admin.siswa.pendaftaran') }}" class="block py-2 pl-14 text-sm hover:text-yellow-400">Approval</a>
                        <a href="{{ route('admin.siswa.index') }}" class="block py-2 pl-14 text-sm hover:text-yellow-400">Daftar Siswa</a>
                    </div>
                </div>
                <a href="{{ route('admin.manajemen-pengajar.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800">
                    <i class="fa-solid fa-chalkboard-user w-6 mr-3 text-center"></i> Pengajar
                </a>

                <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Fitur</div>
                <a href="{{ route('admin.promo.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800">
                    <i class="fa-solid fa-ticket w-6 mr-3 text-center"></i> Kode Promo
                </a>
            </nav>
        </aside>
        
        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center">
                <h2 class="font-bold text-gray-800 uppercase">@yield('title')</h2>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="text-xs font-bold text-gray-500 hover:text-red-700"><i class="fa-solid fa-power-off mr-2"></i> LOGOUT</button>
                </form>
            </header>

           
        <main class="p-6 overflow-y-auto flex-1 bg-gray-50">
            <div class="w-full"> <!-- PAKAI W-FULL AGAR LUAS -->
                @yield('content')
            </div>
        </main>
        </div>
    </div>

    <script>
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
    </script>
</body>
</html>