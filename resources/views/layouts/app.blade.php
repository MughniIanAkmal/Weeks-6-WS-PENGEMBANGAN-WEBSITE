<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Toko - Sistem Kasir & Manajemen Toko Terpadu')</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-brand-badge {
            font-size: 0.65rem;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .nav-link {
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 0.85rem !important;
            border-radius: 6px;
            transition: all 0.15s ease-in-out;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            background-color: #0284c7 !important;
            color: #ffffff !important;
        }

        .card {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .footer {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            background-color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Navbar Utama Terpadu -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-2">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white me-4" href="{{ route('home') }}">
                <span class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-shop"></i>
                </span>
                <span>POS Toko</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle navbar-brand-badge text-uppercase">Pro</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- Menu Utama -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                    {{-- 1. Dashboard / Katalog Produk --}}
                    <li class="nav-item">
                        <a class="nav-link text-light {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-grid-fill me-1"></i> Katalog Produk
                        </a>
                    </li>

                    @auth
                        {{-- 2. Kelola Produk (Admin) --}}
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link text-light {{ request()->routeIs('product.*') ? 'active' : '' }}" href="{{ route('product.index') }}">
                                    <i class="bi bi-box-seam me-1"></i> Kelola Produk
                                </a>
                            </li>
                        @endif

                        {{-- 3. Mesin Kasir & Riwayat (Kasir & Admin) --}}
                        @if (in_array(auth()->user()->role, ['kasir', 'admin']))
                            <li class="nav-item">
                                <a class="nav-link text-light {{ request()->routeIs('cashier.index') ? 'active' : '' }}" href="{{ route('cashier.index') }}">
                                    <i class="bi bi-cart3 me-1 text-warning"></i> Mesin Kasir (POS)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-light {{ request()->routeIs('cashier.history') || request()->routeIs('cashier.receipt') ? 'active' : '' }}" href="{{ route('cashier.history') }}">
                                    <i class="bi bi-clock-history me-1"></i> Riwayat Transaksi
                                </a>
                            </li>
                        @endif

                        {{-- 4. Ekspor Laporan (Kasir & Admin) --}}
                        @if (in_array(auth()->user()->role, ['kasir', 'admin']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-light {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Laporan
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark shadow">
                                    <li><a class="dropdown-item" href="{{ route('reports.index') }}"><i class="bi bi-speedometer2 me-2"></i>Pusat Laporan & Rekap</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.products.pdf') }}" target="_blank"><i class="bi bi-file-pdf me-2 text-danger"></i>Cetak Laporan Produk (PDF)</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reports.transactions.pdf') }}" target="_blank"><i class="bi bi-receipt me-2 text-success"></i>Cetak Rekap Penjualan (PDF)</a></li>
                                </ul>
                            </li>
                        @endif

                        {{-- 5. Manajemen Media & Storage (Admin) --}}
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link text-light {{ request()->routeIs('media.*') ? 'active' : '' }}" href="{{ route('media.index') }}">
                                    <i class="bi bi-folder2-open me-1 text-info"></i> File & Media
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <!-- Menu Kanan: Profil & Auth -->
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2 py-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 30px; height: 30px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="fw-semibold">{{ auth()->user()->name }}</span>
                                <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-danger' : (auth()->user()->role === 'kasir' ? 'bg-warning text-dark' : 'bg-primary') }} text-uppercase ms-1" style="font-size: 0.7rem;">
                                    {{ auth()->user()->role }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li class="dropdown-header text-uppercase small fw-bold">Akun Pengguna</li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile') }}">
                                        <i class="bi bi-person-vcard text-primary"></i> Profil Saya
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header text-uppercase small fw-bold text-muted">Beralih Cepat (Demo Role)</li>
                                <li><a class="dropdown-item small" href="{{ route('quickLogin', 'admin') }}">👑 Login sbg Admin</a></li>
                                <li><a class="dropdown-item small" href="{{ route('quickLogin', 'kasir') }}">🛒 Login sbg Kasir</a></li>
                                <li><a class="dropdown-item small" href="{{ route('quickLogin', 'user') }}">👤 Login sbg User Biasa</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> Logout Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk (Login)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
                                <i class="bi bi-person-plus me-1"></i> Daftar Akun
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="container-fluid px-lg-4 py-4 flex-grow-1">
        {{-- Flash Pesan Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Flash Pesan Error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Flash Info / Warning --}}
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                <div class="flex-grow-1">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Alert Validasi Error --}}
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-x-circle-fill fs-5 me-2"></i>
                    <strong>Terdapat beberapa kesalahan input:</strong>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Sistem Terpadu -->
    <footer class="footer py-3 text-center text-muted small">
        <div class="container-fluid px-lg-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                &copy; {{ date('Y') }} <strong>POS Toko Indonesia</strong> &middot; Sistem Point of Sale & Inventaris Terpadu.
            </div>
            <div class="d-flex gap-3">
                <span class="text-secondary"><i class="bi bi-shield-check text-success me-1"></i>Role-Based Access Control</span>
                <span class="text-secondary"><i class="bi bi-lightning-charge text-warning me-1"></i>Laravel 12 & PHP 8.2+</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
