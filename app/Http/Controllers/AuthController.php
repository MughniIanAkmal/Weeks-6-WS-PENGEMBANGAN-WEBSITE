<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Memproses login manual dengan email dan password.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            $msg = $role === 'admin' 
                ? "Selamat datang, " . Auth::user()->name . "! Anda login sebagai ADMIN (Akses CRUD Aktif)."
                : "Selamat datang, " . Auth::user()->name . "! Anda login sebagai USER BIASA (Hanya Melihat Data).";

            return redirect()->intended(route('home'))->with('success', $msg);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Login cepat 1-klik untuk testing perpindahan role.
     */
    public function quickLogin($role)
    {
        $user = User::where('role', $role)->first();

        if (!$user) {
            return redirect()->back()->with('error', "User dengan role '{$role}' tidak ditemukan.");
        }

        Auth::login($user);
        request()->session()->regenerate();

        $msg = $role === 'admin'
            ? "Login Cepat Berhasil! Anda aktif sebagai ADMIN ({$user->name}) &mdash; Akses CRUD Terbuka."
            : "Login Cepat Berhasil! Anda aktif sebagai {$user->name} ({$role}) &mdash; Mode Hanya Lihat (Read-Only).";

        return redirect()->route('home')->with('success', $msg);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah logout. Sekarang dalam mode tamu (Hanya Lihat).');
    }
}
