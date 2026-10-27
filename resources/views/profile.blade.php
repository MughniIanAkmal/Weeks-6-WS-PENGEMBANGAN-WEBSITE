@extends('layouts.app')

@section('title', 'Profil Pengguna - POS Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold">Welcome, {{ Auth::user()->name }}</h4>
                <small class="text-muted">Informasi Akun Pengguna & Hak Akses Sistem POS Toko</small>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-4">
                    <tbody>
                        <tr>
                            <th style="width: 220px;" class="bg-light">User ID</th>
                            <td><code>{{ $userId }}</code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nama Pengguna</th>
                            <td class="fw-semibold">{{ Auth::user()->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Alamat Email</th>
                            <td>{{ Auth::user()->email }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Role (Peran)</th>
                            <td>
                                <span class="badge {{ Auth::user()->role === 'admin' ? 'bg-danger' : (Auth::user()->role === 'kasir' ? 'bg-info text-dark' : 'bg-secondary') }}">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Hak Akses Sistem</th>
                            <td>
                                @if (Auth::user()->role === 'admin')
                                    <span class="text-success fw-semibold">Akses Penuh: CRUD Produk (Tambah, Edit, Hapus)</span>
                                @elseif (Auth::user()->role === 'kasir')
                                    <span class="text-info fw-semibold">Petugas Kasir: Melihat Katalog & Transaksi</span>
                                @else
                                    <span class="text-muted">Read-Only: Melihat Daftar Produk</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Status Verifikasi Email</th>
                            <td>
                                @if (Auth::user()->hasVerifiedEmail())
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Terverifikasi</span>
                                    <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-outline-warning ms-2 py-0">Kirim Link Verifikasi</a>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Kembali ke Katalog</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
