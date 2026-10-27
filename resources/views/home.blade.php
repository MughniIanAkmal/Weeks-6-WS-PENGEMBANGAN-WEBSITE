@extends('layouts.app')

@section('title', 'Katalog Produk & Praktikum Web Framework')

@section('content')
<!-- Bar Status Autentikasi & Hak Akses Role -->
<div class="card mb-3 border">
    <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
        <span class="small text-muted">Status:</span>
        @if (auth()->check())
            @if (auth()->user()->role === 'admin')
                <span class="badge bg-success">
                    {{ auth()->user()->name }} (ADMIN)
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle small">
                    Akses CRUD & Kasir Aktif
                </span>
                <a href="{{ route('cashier.index') }}" class="btn btn-primary btn-sm py-0 ms-auto">
                    🛒 Buka Mesin Kasir (POS)
                </a>
            @elseif (auth()->user()->role === 'kasir')
                <span class="badge bg-info text-dark">
                    {{ auth()->user()->name }} (KASIR)
                </span>
                <span class="badge bg-info-subtle text-dark border border-info-subtle small">
                    Akses Transaksi Penjualan & Cetak Struk
                </span>
                <a href="{{ route('cashier.index') }}" class="btn btn-primary btn-sm py-0 ms-auto">
                    🛒 Buka Mesin Kasir (POS)
                </a>
            @else
                <span class="badge bg-secondary">
                    {{ auth()->user()->name }} (USER BIASA)
                </span>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle small">
                    Hanya Lihat (Tidak Bisa CRUD)
                </span>
            @endif
        @else
            <span class="badge bg-light text-dark border">
                Tamu (Belum Login)
            </span>
            <span class="badge bg-warning-subtle text-dark border border-warning-subtle small">
                Hanya Lihat (Login sebagai Admin melalui menu di atas untuk melakukan CRUD)
            </span>
        @endif
    </div>
</div>

<!-- Filter dan Pencarian Produk -->
<div class="card mb-3 border">
    <div class="card-body py-2">
        <form action="{{ route('home') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama produk atau SKU..." value="{{ $search }}">
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
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-secondary btn-sm px-3">Cari</button>
                @if ($search || $categoryId)
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Utama: Daftar Nama-Nama Produk Toko -->
<div class="card border mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold text-dark">Daftar Produk Toko</span>
            <span class="text-muted small ms-2">(Total: {{ $stats['total_products'] }} produk)</span>
        </div>
        <div>
            {{-- Tombol Tambah Produk menggunakan Directive @can (Acara 24 Authorization) --}}
            @can('create', App\Models\Product::class)
                <a href="{{ route('product.create') }}" class="btn btn-success btn-sm py-1">
                    + Tambah Produk
                </a>
            @else
                <button class="btn btn-secondary btn-sm py-1 disabled" title="Hanya Admin yang dapat menambah produk">
                    + Tambah Produk (Terkunci)
                </button>
            @endcan
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">ID</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th style="width: 90px;" class="text-center">Stok</th>
                    <th style="width: 150px;" class="text-center">Aksi CRUD</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td class="text-center text-muted small">{{ $p->id }}</td>
                        <td class="fw-semibold text-dark">
                            {{ $p->name }}
                        </td>
                        <td><code>{{ $p->sku }}</code></td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->category->name ?? '-' }}</span>
                        </td>
                        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $p->stock < 15 ? 'bg-danger' : 'bg-secondary' }}">
                                {{ $p->stock }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- Pengecekan Hak Akses menggunakan Policy @can('update', $p) (Acara 24) --}}
                            @can('update', $p)
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $p->id) }}" class="btn btn-outline-warning py-0 px-2" title="Edit Data">
                                         Edit
                                    </a>
                                    <form action="{{ route('product.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk {{ $p->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger py-0 px-2" title="Hapus Data">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="badge bg-light text-muted border small" title="User biasa tidak memiliki hak akses CRUD">
                                    Hanya Lihat
                                </span>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Data produk tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="card-footer bg-white py-2">
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Informasi Hak Akses bagi Mahasiswa/Dosen Penguji -->
<div class="alert alert-light border small text-muted mb-0">
    <strong>Keterangan Hak Akses (Role Permission):</strong>
    <ul class="mb-0 ps-3 mt-1">
        <li><strong>Admin (`role = 'admin'`)</strong>: Memiliki hak penuh untuk melakukan operasi <strong>CRUD</strong> (Menambah produk baru, mengubah/edit produk, dan menghapus produk).</li>
        <li><strong>User Biasa (`role = 'user'`) / Tamu</strong>: Bersifat <strong>Read-Only</strong> (hanya dapat melihat daftar nama produk, harga, dan stok tanpa tombol aksi manipulasi). Jika mencoba membuka URL formulir tambah/edit secara manual, sistem akan otomatis menolak akses.</li>
    </ul>
</div>
@endsection
