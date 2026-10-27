@extends('layouts.app')

@section('title', 'Katalog Produk & Dashboard - POS Toko')

@section('content')
<!-- Header Banner & Quick Actions -->
<div class="row align-items-center mb-4 g-3">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-shop text-primary me-2"></i>Katalog & Daftar Produk Toko
        </h3>
        <p class="text-muted small mb-0">
            Sistem katalog terintegrasi dengan upload foto produk, thumbnail otomatis, dan pemantauan stok real-time.
        </p>
    </div>
    <div class="col-md-5 d-flex justify-content-md-end gap-2 flex-wrap">
        {{-- Tombol Ekspor PDF Laporan --}}
        <a href="{{ route('reports.products.pdf') }}" target="_blank" class="btn btn-outline-danger btn-sm px-3 shadow-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ekspor PDF
        </a>

        @auth
            @if (in_array(auth()->user()->role, ['kasir', 'admin']))
                <a href="{{ route('cashier.index') }}" class="btn btn-warning btn-sm px-3 shadow-sm fw-semibold">
                    <i class="bi bi-cart-check-fill me-1"></i> Buka Kasir (POS)
                </a>
            @endif
        @endauth

        @can('create', App\Models\Product::class)
            <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
                + Tambah Produk
            </a>
        @else
            <button class="btn btn-secondary btn-sm px-3 shadow-sm disabled" title="Hanya Admin yang dapat menambah produk">
                <i class="bi bi-lock me-1"></i> Tambah Produk (Terkunci)
            </button>
        @endcan
    </div>
</div>

<!-- Kartu Statistik Cepat Inventaris -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body py-3">
                <span class="text-muted small fw-semibold text-uppercase">Total Produk</span>
                <h4 class="fw-bold my-1 text-dark">{{ number_format($stats['total_products']) }}</h4>
                <div class="small text-muted"><i class="bi bi-box me-1"></i>Varian item aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body py-3">
                <span class="text-muted small fw-semibold text-uppercase">Total Stok Fisik</span>
                <h4 class="fw-bold my-1 text-success">{{ number_format($stats['total_stock']) }}</h4>
                <div class="small text-muted"><i class="bi bi-check2-circle me-1"></i>Unit tersedia</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm border-start border-info border-4">
            <div class="card-body py-3">
                <span class="text-muted small fw-semibold text-uppercase">Rata-rata Harga</span>
                <h4 class="fw-bold my-1 text-info">Rp {{ number_format($stats['avg_price'], 0, ',', '.') }}</h4>
                <div class="small text-muted"><i class="bi bi-tag me-1"></i>Harga katalog</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm border-start border-secondary border-4">
            <div class="card-body py-3">
                <span class="text-muted small fw-semibold text-uppercase">Kategori Produk</span>
                <h4 class="fw-bold my-1 text-secondary">{{ number_format($stats['total_category']) }}</h4>
                <div class="small text-muted"><i class="bi bi-tags me-1"></i>Klasifikasi toko</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter dan Pencarian Produk -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form action="{{ route('home') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama produk atau kode SKU..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3 flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if ($search || $categoryId)
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tab Navigasi Tampilan (Grid Kartu Gambar vs Tabel Data) -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <ul class="nav nav-pills" id="productViewTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active btn-sm py-1 px-3" id="grid-tab" data-bs-toggle="pill" data-bs-target="#gridView" type="button" role="tab">
                <i class="bi bi-grid me-1"></i> Galeri Katalog
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-sm py-1 px-3" id="table-tab" data-bs-toggle="pill" data-bs-target="#tableView" type="button" role="tab">
                <i class="bi bi-table me-1"></i> Tabel Data
            </button>
        </li>
    </ul>
    <span class="text-muted small">Menampilkan {{ $products->count() }} dari {{ $stats['total_products'] }} produk</span>
</div>

<div class="tab-content" id="productViewContent">
    <!-- 1. Tampilan Grid Kartu Katalog Gambar -->
    <div class="tab-pane fade show active" id="gridView" role="tabpanel">
        <div class="row g-3">
            @forelse ($products as $p)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <!-- Foto Produk / Thumbnail (Intervention Image v3 300x200) -->
                        <div class="position-relative bg-light text-center" style="height: 180px; overflow: hidden;">
                            @if ($p->thumbnail_url)
                                <img src="{{ $p->thumbnail_url }}" alt="{{ $p->name }}" class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted bg-light">
                                    <i class="bi bi-image fs-1 text-secondary opacity-50"></i>
                                    <span class="small mt-1 text-secondary">Belum ada foto</span>
                                </div>
                            @endif

                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75">
                                {{ $p->category->name ?? 'Umum' }}
                            </span>

                            <span class="position-absolute top-0 end-0 m-2 badge {{ $p->stock <= 0 ? 'bg-danger' : ($p->stock < 10 ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ $p->stock <= 0 ? 'Habis' : 'Stok: ' . $p->stock }}
                            </span>
                        </div>

                        <div class="card-body p-3 d-flex flex-column">
                            <small class="text-muted text-uppercase" style="font-size: 0.75rem;">SKU: {{ $p->sku }}</small>
                            <h6 class="fw-bold text-dark mt-1 mb-2 text-truncate" title="{{ $p->name }}">
                                <a href="{{ route('product.show', $p->id) }}" class="text-decoration-none text-dark">
                                    {{ $p->name }}
                                </a>
                            </h6>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary fs-6">
                                    Rp {{ number_format($p->price, 0, ',', '.') }}
                                </span>

                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.show', $p->id) }}" class="btn btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('update', $p)
                                        <a href="{{ route('product.edit', $p->id) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm text-center py-5">
                        <i class="bi bi-search fs-1 text-muted mb-2"></i>
                        <h5 class="fw-bold text-secondary">Produk Tidak Ditemukan</h5>
                        <p class="text-muted small mb-0">Coba kata kunci lain atau reset filter kategori.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. Tampilan Tabel Data Produk Lengkap -->
    <div class="tab-pane fade" id="tableView" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Foto</th>
                            <th>Nama Produk</th>
                            <th>Kode SKU</th>
                            <th>Kategori</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-center" style="width: 90px;">Stok</th>
                            <th class="text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $p)
                            <tr>
                                <td>
                                    @if ($p->thumbnail_url)
                                        <img src="{{ $p->thumbnail_url }}" alt="{{ $p->name }}" class="rounded shadow-sm" style="width: 45px; height: 35px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center" style="width: 45px; height: 35px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('product.show', $p->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $p->name }}
                                    </a>
                                </td>
                                <td><code>{{ $p->sku }}</code></td>
                                <td><span class="badge bg-light text-dark border">{{ $p->category->name ?? '-' }}</span></td>
                                <td class="text-end fw-semibold text-primary">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $p->stock <= 0 ? 'bg-danger' : ($p->stock < 10 ? 'bg-warning text-dark' : 'bg-success') }}">
                                        {{ $p->stock }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.show', $p->id) }}" class="btn btn-outline-secondary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $p)
                                            <a href="{{ route('product.edit', $p->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('product.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk {{ $p->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Data produk tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination Links -->
@if ($products->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
@endif
@endsection
