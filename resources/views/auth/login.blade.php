@extends('layouts.app')

@section('title', 'Login Pengguna - POS Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border p-4">
            <h4 class="fw-bold mb-1 text-center">Login POS Toko</h4>
            <p class="text-muted small text-center mb-4">
                Masuk untuk mengelola data produk atau uji hak akses.
            </p>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', 'admin@postoko.com') }}" placeholder="Masukkan email..." required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="password123" placeholder="Masukkan password..." required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3">
                    Login
                </button>
                <div class="text-center mb-3">
                    <span class="text-muted small">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="small fw-semibold">Daftar Akun Baru</a>
                </div>
            </form>

            <div class="border-top pt-3">
                <div class="fw-semibold small mb-2 text-dark">Quick Login Pengujian Praktikum:</div>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <a href="{{ route('quickLogin', 'admin') }}" class="btn btn-outline-danger btn-sm">
                        Admin
                    </a>
                    <a href="{{ route('quickLogin', 'kasir') }}" class="btn btn-outline-info btn-sm">
                        Kasir
                    </a>
                    <a href="{{ route('quickLogin', 'user') }}" class="btn btn-outline-secondary btn-sm">
                        User Biasa
                    </a>
                </div>
                <div class="text-muted small">
                    <div class="fw-semibold mb-1">Kredensial Manual:</div>
                    <ul class="mb-0 ps-3">
                        <li><code>admin@postoko.com</code> / <code>password123</code> (Admin)</li>
                        <li><code>kasir1@postoko.com</code> / <code>password123</code> (Kasir)</li>
                        <li><code>johndoe@example.com</code> / <code>password123</code> (User)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
