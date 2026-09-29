<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah terautentikasi (login)
        if (!auth()->check()) {
            abort(401, 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = auth()->user();

        // Jika tidak ada role spesifik yang didefinisikan pada route, izinkan lewat
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah role user saat ini terdapat dalam daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak! Anda (Role: ' . $user->role . ') tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
