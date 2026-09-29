@extends('layouts.app')

@section('title', 'Acara 17 - Query Builder')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">ACARA 17: Query Builder</h4>
        <small class="text-muted">Operasi CRUD tabel products menggunakan <code>DB::table()</code></small>
    </div>
    <div>
        <a href="{{ route('acara17.demos') }}" class="btn btn-outline-secondary btn-sm me-1">Contoh Kode PDF</a>
        @if (auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('acara17.products.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('acara17.products.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama atau SKU..." value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select form-control-sm">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                @if ($search || $categoryId)
                    <a href="{{ route('acara17.products.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-bordered table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Kategori (Join)</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="width: 130px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->category_name }}</td>
                        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td>{{ $p->stock }}</td>
                        <td class="text-center">
                            @if (auth()->check() && auth()->user()->role === 'admin')
                                <a href="{{ route('acara17.products.edit', $p->id) }}" class="btn btn-warning btn-sm py-0">Edit</a>
                                <form action="{{ route('acara17.products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm py-0">Hapus</button>
                                </form>
                            @else
                                <span class="badge bg-light text-muted border">Hanya Lihat</span>
                            @endif
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
    @if ($products->hasPages())
        <div class="card-footer bg-white py-2">
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
