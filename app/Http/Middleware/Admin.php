<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Admin
{
    /**
     * Handle an incoming request (Acara 21 Poin 2).
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return $next($request);
            } else {
                abort(403, 'Akses Ditolak! Halaman ini hanya dapat diakses oleh Admin.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu sebagai Admin.');
        }
    }

    /**
     * Siklus hidup middleware: terminate() dipanggil setelah response dikirim ke client (Acara 21 Poin 6).
     */
    public function terminate($request, $response)
    {
        Log::info('Request selesai via Admin Middleware', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user' => Auth::check() ? Auth::user()->email : 'Guest',
        ]);
    }
}
