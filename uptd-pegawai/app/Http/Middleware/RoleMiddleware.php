<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            abort(403, 'Akses Ditolak.');
        }

        // Jika Admin, lolos semua akses
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Jika bukan admin, cek apakah sesuai role yang dibutuhkan
        if (Auth::user()->role !== $role) {
            abort(403, 'Akses Ditolak.');
        }

        return $next($request);
    }
}
