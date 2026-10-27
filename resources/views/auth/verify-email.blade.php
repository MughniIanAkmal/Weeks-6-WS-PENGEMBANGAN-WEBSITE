@extends('layouts.app')

@section('title', 'Verifikasi Email - POS Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border p-4 text-center">
            <h4 class="fw-bold mb-2">Verifikasi Email Anda</h4>
            <p class="text-muted small mb-4">
                Silakan periksa kotak masuk email Anda untuk melakukan verifikasi akun POS Toko. Jika Anda belum menerima email, klik tombol di bawah untuk mengirim ulang.
            </p>

            @if (session('message'))
                <div class="alert alert-success small">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-warning fw-semibold px-4">
                    Resend Verification Email
                </button>
            </form>

            <div class="mt-4 pt-3 border-top small text-muted">
                Sudah verifikasi atau ingin kembali ke profil? <a href="{{ route('profile') }}">Buka Profil</a>
            </div>
        </div>
    </div>
</div>
@endsection
