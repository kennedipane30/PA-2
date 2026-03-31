<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Spekta Academy Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-red-900 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">SPEKTA ACADEMY - Admin</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-800 px-4 py-2 rounded hover:bg-red-700 transition text-sm">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-red-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="text-red-900 text-4xl">👥</div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Students</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="text-blue-600 text-4xl">🎓</div>
                </div>
            </div>

            <!-- Total Teachers -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Teachers</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_teachers'] }}</p>
                    </div>
                    <div class="text-green-600 text-4xl">👨‍🏫</div>
                </div>
            </div>

            <!-- Total Classes -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Classes</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_classes'] }}</p>
                    </div>
                    <div class="text-purple-600 text-4xl">📚</div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Pending Payments</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_payments'] }}</p>
                    </div>
                    <div class="text-orange-600 text-4xl">⏳</div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-emerald-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="text-emerald-600 text-4xl">💰</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-6 text-gray-800">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                
                <!-- Manage Users -->
                <a href="{{ route('admin.siswa.index') }}" 
                   class="bg-red-900 text-white p-4 rounded-lg text-center hover:bg-red-800 transition shadow">
                    <div class="text-3xl mb-2">👥</div>
                    <div class="text-sm font-semibold">Manage Users</div>
                </a>

                <!-- Manage Classes -->
                <a href="{{ route('admin.jadwal.index') }}" 
                   class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700 transition shadow">
                    <div class="text-3xl mb-2">📚</div>
                    <div class="text-sm font-semibold">Manage Classes</div>
                </a>

                <!-- Verify Payments -->
                <a href="{{ route('admin.pembayaran.index') }}" 
                   class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition shadow">
                    <div class="text-3xl mb-2">💳</div>
                    <div class="text-sm font-semibold">Verify Payments</div>
                </a>
                
                <!-- Manage Gallery -->
                <a href="{{ route('admin.galeri.index') }}" 
                   class="bg-yellow-600 text-white p-4 rounded-lg text-center hover:bg-yellow-700 transition shadow">
                    <div class="text-3xl mb-2">🖼️</div>
                    <div class="text-sm font-semibold">Manage Gallery</div>
                </a>

                <!-- Reports -->
                <a href="#" 
                   class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700 transition shadow">
                    <div class="text-3xl mb-2">📊</div>
                    <div class="text-sm font-semibold">Reports</div>
                </a>

            </div>
        </div>
    </div>
</body>
</html>