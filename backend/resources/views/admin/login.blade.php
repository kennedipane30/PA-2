<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Spekta Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-red-900">SPEKTA ACADEMY</h1>
                <p class="text-gray-600 mt-2">Admin Dashboard Login</p>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-900">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-900">
                </div>

                <button type="submit" 
                        class="w-full bg-red-900 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-800 transition">
                    LOGIN
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Default Admin:</p>
                <p class="font-mono">admin@spekta.com / Admin@123</p>
            </div>
        </div>
    </div>
</body>
</html>
