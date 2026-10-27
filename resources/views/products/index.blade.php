@extends('layouts.app')

@section('title', 'Manajemen Produk - POS Toko')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-box-seam text-primary me-2"></i>Manajemen Data Produk
        </h3>
        <p class="text-muted small mb-0">
            Kelola inventaris, kategori, harga jual, serta foto produk toko Anda secara terpusat.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.products.pdf') }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF Produk
        </a>
        @can('create', App\Models\Product::class)
            <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
            </a>
        @endcan
    </div>
</div>

<!-- Filter Pencarian -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form action="{{ route('product.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau SKU..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if (!empty($search) || !empty($categoryId))
                    <a href="{{ route('product.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data Produk -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">Foto</th>
                    <th>Nama Produk</th>
                    <th>SKU / Kode</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-center" style="width: 100px;">Stok</th>
                    <th style="width: 150px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>
                            @if ($p->thumbnail_url)
                                <img src="{{ $p->thumbnail_url }}" alt="{{ $p->name }}" class="rounded shadow-sm" style="width: 48px; height: 36px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded text-muted d-flex align-items-center justify-content-center" style="width: 48px; height: 36px;">
                                    <i class="bi bi-image text-secondary"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('product.show', $p->id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $p->name }}
                            </a>
                        </td>
                        <td><code>{{ $p->sku }}</code></td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->category->name ?? '-' }}</span>
                        </td>
                        <td class="text-end fw-bold text-primary">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
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
                                @endcan
                                @can('delete', $p)
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data produk yang tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="card-footer bg-white py-3 border-0">
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
