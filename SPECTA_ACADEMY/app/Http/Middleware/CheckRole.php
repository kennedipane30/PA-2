<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah nama role user sama dengan parameter $role
        if ($request->user()->role->nama_role !== $role) {
            abort(403, 'Akses ditolak!');
        }

        return $next($request);
    }
}
