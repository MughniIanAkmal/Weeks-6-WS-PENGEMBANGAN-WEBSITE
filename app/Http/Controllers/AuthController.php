<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan form login (Acara 22).
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Memproses login manual dengan email dan password (Acara 22 Hal. 10-11).
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
     * Menampilkan form registrasi akun (Acara 22).
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Memproses registrasi pengguna baru (Acara 22 Hal. 11).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'role'     => 'required|in:admin,user,kasir',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Kirim email notifikasi verifikasi jika diperlukan
        $user->sendEmailVerificationNotification();

        // Login otomatis pengguna yang baru mendaftar
        Auth::login($user);

        return redirect()->route('home')->with('success', "Registrasi berhasil! Selamat datang, {$user->name} ({$user->role}).");
    }

    /**
     * Menampilkan halaman profil pengguna yang sedang login (Acara 23 Hal. 17).
     */
    public function showProfile()
    {
        $user = Auth::user(); // Mendapatkan user yang sedang login
        $userId = Auth::id(); // Mengambil ID pengguna

        return view('profile', compact('user', 'userId'));
    }

    /**
     * Halaman pemberitahuan verifikasi email (Acara 22 Hal. 13-14).
     */
    public function verifyNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Memproses link verifikasi email (Acara 22 Hal. 14).
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('profile')->with('success', 'Email Anda telah berhasil diverifikasi!');
    }

    /**
     * Mengirim ulang link verifikasi email (Acara 22 Hal. 14).
     */
    public function resendVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Tautan verifikasi email baru telah dikirimkan!');
    }

    /**
     * Quick Login untuk memudahkan pengujian praktikum role switching.
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
            : "Login Cepat Berhasil! Anda aktif sebagai {$user->name} ({$role}) &mdash; Mode Hanya Lihat.";

        return redirect()->route('home')->with('success', $msg);
    }

    /**
     * Logout manual (Acara 22 Hal. 11 & Acara 23 Hal. 18).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil logout. Sekarang dalam mode tamu.');
    }
}
