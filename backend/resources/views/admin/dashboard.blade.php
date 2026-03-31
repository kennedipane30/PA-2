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
    <nav class="bg-red-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">SPEKTA ACADEMY - Admin</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-800 px-4 py-2 rounded hover:bg-red-700 transition text-sm">
                        Logout
                    </button>
                </form>
                <!-- Alternative: Direct link -->
                <!-- <a href="{{ route('admin.logout.get') }}" class="bg-red-800 px-4 py-2 rounded hover:bg-red-700 transition text-sm">Logout</a> -->
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-red-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="text-red-900 text-4xl">👥</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Students</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="text-blue-600 text-4xl">🎓</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Teachers</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_teachers'] }}</p>
                    </div>
                    <div class="text-green-600 text-4xl">👨‍🏫</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Classes</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_classes'] }}</p>
                    </div>
                    <div class="text-purple-600 text-4xl">📚</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Pending Payments</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_payments'] }}</p>
                    </div>
                    <div class="text-orange-600 text-4xl">⏳</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="text-green-600 text-4xl">💰</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-red-900 text-white p-4 rounded-lg text-center hover:bg-red-800 transition">
                    <div class="text-3xl mb-2">👥</div>
                    <div>Manage Users</div>
                </a>

                <a href="{{ route('admin.classes.index') }}" 
                   class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700 transition">
                    <div class="text-3xl mb-2">📚</div>
                    <div>Manage Classes</div>
                </a>

                <a href="{{ route('admin.payments.index') }}" 
                   class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition">
                    <div class="text-3xl mb-2">💳</div>
                    <div>Verify Payments</div>
                </a>

                <a href="#" 
                   class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700 transition">
                    <div class="text-3xl mb-2">📊</div>
                    <div>Reports</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
