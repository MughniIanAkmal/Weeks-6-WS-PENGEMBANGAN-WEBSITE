<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request with role parameter (Acara 21 Poin 5).
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
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak!'], 403);
            }
            abort(403, 'Akses ditolak! Anda (Role: ' . $user->role . ') tidak memiliki izin. Membutuhkan role: ' . implode(' atau ', $roles));
        }

        return $next($request);
    }

    /**
     * Siklus hidup middleware: terminate() (Acara 21 Poin 6).
     */
    public function terminate($request, $response)
    {
        Log::info('RoleMiddleware terminate dijalankan', [
            'url'    => $request->fullUrl(),
            'user'   => auth()->check() ? auth()->user()->email : 'guest',
            'status' => $response->getStatusCode(),
        ]);
    }
}
