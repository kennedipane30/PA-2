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
        .bg-spekta-dark { background-color: #7a0000; }
        .text-spekta { color: #990000; }
<<<<<<< HEAD
        .active-menu { background-color: #7a0000; border-left: 4px solid #fbbf24; } 
        
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #990000; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #7a0000; border-radius: 10px; }
=======
        .border-spekta { border-color: #990000; }
        .rotate-180 { transform: rotate(180deg); }
        #siswa-menu, #pengajar-menu, #tryout-menu {
            transition: all 0.3s ease-in-out;
        }
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-72 bg-spekta text-white flex-shrink-0 shadow-xl flex flex-col">
            <!-- Logo Area -->
            <div class="p-8 border-b border-red-800 flex flex-col items-center justify-center">
                <h1 class="text-3xl font-black tracking-tighter">SPEKTA</h1>
                <p class="text-[10px] uppercase tracking-[0.3em] opacity-80 -mt-1 font-light">Academy Management</p>
            </div>
<<<<<<< HEAD
            
            <!-- Navigation Area -->
            <nav class="flex-1 mt-4 overflow-y-auto sidebar-scroll pb-10">
                
                {{-- ========================================== --}}
                {{-- MENU UNTUK ADMIN (Role ID: 1)              --}}
                {{-- ========================================== --}}
                @if(Auth::user()->role_id == 1)
                    <div class="px-6 py-2 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->routeIs('admin.dashboard') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-gauge-high w-6 text-center mr-3"></i> Dashboard
                    </a>

                    <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Akademik</div>
                    <a href="{{ route('admin.program.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('admin/program*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-layer-group w-6 text-center mr-3"></i> Manajemen Kelas
                    </a>
                    <a href="{{ route('admin.jadwal.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('admin/jadwal*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-calendar-days w-6 text-center mr-3"></i> Jadwal Kelas
                    </a>

                    <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">User Management</div>
=======

            <nav class="mt-4 overflow-y-auto h-full pb-20">
                @if(Auth::user()->role_id == 1) <!-- MENU ADMIN -->
                    <a href="{{ route('admin.dashboard') }}" class="block py-3 px-6 hover:bg-red-800">🏠 Dashboard</a>
                    <a href="{{ route('admin.jadwal.index') }}" class="block py-3 px-6 hover:bg-red-800">📅 Jadwal Kelas</a>

                    <!-- DROPDOWN SISWA -->
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
                    <div class="relative">
                        <button onclick="toggleDropdown('siswa-menu', 'siswa-arrow')" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 transition focus:outline-none">
                            <span class="flex items-center"><i class="fa-solid fa-user-graduate w-6 text-center mr-3"></i> Siswa</span>
                            <i id="siswa-arrow" class="fa-solid fa-chevron-down text-xs transition-transform duration-300"></i>
                        </button>
<<<<<<< HEAD
                        <div id="siswa-menu" class="hidden bg-black bg-opacity-10 py-2">
                            <a href="{{ route('admin.siswa.pendaftaran') }}" class="flex items-center py-2 pl-14 pr-6 text-sm hover:text-yellow-400 transition">
                                <span>Approval</span>
                                @php $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count(); @endphp
                                @if($pendingCount > 0) 
                                    <span class="ml-auto bg-yellow-500 text-red-900 text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span> 
                                @endif
                            </a>
                            <a href="{{ route('admin.siswa.index') }}" class="block py-2 pl-14 pr-6 text-sm hover:text-yellow-400 transition">Daftar Siswa</a>
=======
                        <div id="siswa-menu" class="hidden bg-red-950/30">
                            <a href="{{ route('admin.siswa.index') }}" class="block py-2 pl-12 pr-6 text-sm hover:text-yellow-400">○ Semua Siswa</a>
                            <a href="{{ route('admin.siswa.pendaftaran') }}" class="block py-2 pl-12 pr-6 text-sm hover:text-yellow-400 flex justify-between items-center">
                                <span>○ Tambah Kelas</span>
                                @php $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count(); @endphp
                                @if($pendingCount > 0)
                                    <span class="bg-yellow-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span>
                                @endif
                            </a>
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
                        </div>
                    </div>
                    <a href="{{ route('admin.manajemen-pengajar.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('admin/manajemen-pengajar*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-chalkboard-user w-6 text-center mr-3"></i> Pengajar
                    </a>

<<<<<<< HEAD
                    <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Fitur Pendukung</div>
                    <a href="{{ route('admin.promo.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('admin/promo*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-ticket w-6 text-center mr-3"></i> Kode Promo
                    </a>
                    <a href="{{ route('admin.galeri.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('admin/galeri*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-images w-6 text-center mr-3"></i> Galeri & Info
                    </a>
=======
                    <a href="{{ route('admin.promo.index') }}" class="block py-3 px-6 hover:bg-red-800">🎁 Kode Promo</a>
                    <a href="{{ route('admin.manajemen-pengajar.index') }}" class="block py-3 px-6 hover:bg-red-800">👨‍🏫 Manajemen Pengajar</a>
                    <a href="{{ route('admin.galeri.index') }}" class="block py-3 px-6 hover:bg-red-800">🖼️ Galeri & Info</a>
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54

                {{-- ========================================== --}}
                {{-- MENU UNTUK PENGAJAR (Role ID: 2)           --}}
                {{-- ========================================== --}}
                @elseif(Auth::user()->role_id == 2)
                    <div class="px-6 py-2 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Utama</div>
                    <a href="{{ route('pengajar.dashboard') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->routeIs('pengajar.dashboard') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-gauge-high w-6 text-center mr-3"></i> Dashboard
                    </a>

<<<<<<< HEAD
                    <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Materi & Belajar</div>
                    <!-- FITUR TAMBAH MATERI -->
                    <a href="{{ route('pengajar.materi.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('pengajar/materi*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-book w-6 text-center mr-3"></i> Kelola Materi
                    </a>
                    <a href="{{ route('pengajar.jadwal.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('pengajar/jadwal*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-calendar-day w-6 text-center mr-3"></i> Jadwal Mengajar
                    </a>
=======
                    <!-- DROPDOWN ABSENSI -->
                    <div class="relative">
                        <button onclick="togglePengajarDropdown()" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 transition focus:outline-none">
                            <span class="flex items-center">📝 Absensi Siswa</span>
                            <svg id="pengajar-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="pengajar-menu" class="hidden bg-red-950/30">
                            @foreach(\App\Models\ClassModel::all() as $c)
                                {{-- Pastikan menggunakan $c->id karena di model didefinisikan $primaryKey = 'id' --}}
                                <a href="{{ route('pengajar.absensi.show', ['class_id' => $c->id]) }}" class="block py-2 pl-12 pr-6 text-[11px] hover:text-yellow-400">
                                    ○ {{ $c->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54

                    <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Evaluasi & Siswa</div>
                    <a href="{{ route('pengajar.evaluasi.tugas.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('pengajar/evaluasi/tugas*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-file-signature w-6 text-center mr-3"></i> Tugas & Nilai
                    </a>
                    <a href="{{ route('pengajar.evaluasi.tryout.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('pengajar/evaluasi/tryout*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-trophy w-6 text-center mr-3"></i> Kelola Tryout
                    </a>
                    <a href="{{ route('pengajar.siswa.absensi.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('pengajar/siswa/absensi*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-calendar-check w-6 text-center mr-3"></i> Absensi Siswa
                    </a>

<<<<<<< HEAD
                    <div class="px-6 py-2 mt-4 text-[10px] font-bold text-red-300 uppercase tracking-widest opacity-60">Interaksi</div>
                    <a href="{{ route('pengajar.komunikasi.diskusi.index') }}" class="flex items-center py-3 px-6 hover:bg-red-800 transition-all {{ request()->is('pengajar/komunikasi/diskusi*') ? 'active-menu' : '' }}">
                        <i class="fa-solid fa-comments w-6 text-center mr-3"></i> Forum Diskusi
                    </a>
                @endif
            </nav>

            <!-- Bottom User Profile -->
            <div class="p-4 bg-red-950 flex items-center border-t border-red-800">
                <div class="w-10 h-10 rounded-full bg-red-800 flex items-center justify-center text-xl font-bold border border-red-700">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3 overflow-hidden">
                    <p class="text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] opacity-60 truncate">
                        {{ Auth::user()->role_id == 1 ? 'Administrator' : 'Pengajar' }}
                    </p>
                </div>
            </div>
        </aside>
        
        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center z-10">
                <div class="flex items-center">
                    <i class="fa-solid fa-bars text-gray-400 mr-4 cursor-pointer hover:text-red-700 transition"></i>
                    <h2 class="font-extrabold text-gray-800 tracking-tight text-lg">@yield('title')</h2>
                </div>
                
                <div class="flex items-center space-x-6">
=======
                    <!-- DROPDOWN INPUT SOAL TO -->
                    <div class="relative">
                        <button onclick="toggleTryoutDropdown()" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 transition focus:outline-none">
                            <span class="flex items-center">⏱️ Input Soal TO</span>
                            <svg id="tryout-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="tryout-menu" class="hidden bg-red-950/30">
                            @foreach(\App\Models\ClassModel::all() as $c)
                                <a href="{{ route('pengajar.tryout.create', ['class_id' => $c->id]) }}" class="block py-2 pl-12 pr-6 text-[11px] hover:text-yellow-400">
                                    ○ {{ $c->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('pengajar.tryout.nilai') }}" class="block py-3 px-6 hover:bg-red-800">📊 Lihat Nilai</a>
                @endif
            </nav>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow px-8 py-4 flex justify-between items-center z-10">
                <h2 class="font-bold text-gray-700 uppercase">@yield('title')</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-spekta font-medium">
                            {{ Auth::user()->role ? Auth::user()->role->name : 'No Role' }}
                        </p>
                    </div>
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="group flex items-center text-xs font-bold text-gray-500 hover:text-red-700 transition">
                            <i class="fa-solid fa-power-off mr-2 group-hover:rotate-90 transition-transform"></i> KELUAR SISTEM
                        </button>
                    </form>
                </div>
            </header>

<<<<<<< HEAD
            <main class="p-8 overflow-y-auto bg-gray-50 flex-1">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
=======
            <main class="p-8 overflow-y-auto bg-gray-50 flex-1 pb-24">
                @yield('content')
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
            </main>
        </div>
    </div>

    <script>
        function toggleDropdown(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);
            menu.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }

        window.onload = function() {
            const currentUrl = window.location.href;
<<<<<<< HEAD
            if (currentUrl.includes('admin/siswa')) {
                const siswaMenu = document.getElementById('siswa-menu');
                if(siswaMenu) siswaMenu.classList.remove('hidden');
                const siswaArrow = document.getElementById('siswa-arrow');
                if(siswaArrow) siswaArrow.classList.add('rotate-180');
=======
            if (currentUrl.includes('admin/siswa') || currentUrl.includes('admin/pendaftaran')) {
                const el = document.getElementById('siswa-menu');
                const ar = document.getElementById('siswa-arrow');
                if(el) el.classList.remove('hidden');
                if(ar) ar.classList.add('rotate-180');
            }
            if (currentUrl.includes('pengajar/absensi')) {
                const el = document.getElementById('pengajar-menu');
                const ar = document.getElementById('pengajar-arrow');
                if(el) el.classList.remove('hidden');
                if(ar) ar.classList.add('rotate-180');
            }
            if (currentUrl.includes('pengajar/soal-tryout') || currentUrl.includes('pengajar/tryout')) {
                const el = document.getElementById('tryout-menu');
                const ar = document.getElementById('tryout-arrow');
                if(el) el.classList.remove('hidden');
                if(ar) ar.classList.add('rotate-180');
>>>>>>> b33bd9ca539f5e9c5320c729d852cb06393aaa54
            }
        }
    </script>
</body>
</html>
