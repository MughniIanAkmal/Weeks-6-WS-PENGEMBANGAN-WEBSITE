@extends('layouts.app')

@section('title', 'ACARA 13 - Resource Controller Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">ACARA 13: Resource Controller</h4>
        <small class="text-muted">CRUD Produk via <code>Route::resource('product', ProductController::class)</code></small>
    </div>
    <div>
        <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="width: 170px;" class="text-center">Aksi (Resource)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>
                            <a href="{{ route('product.show', $p->id) }}" class="fw-semibold text-decoration-none">
                                {{ $p->name }}
                            </a>
                        </td>
                        <td><code>{{ $p->sku }}</code></td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td>{{ $p->stock }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.show', $p->id) }}" class="btn btn-outline-info" title="show()">Detail</a>
                                <a href="{{ route('product.edit', $p->id) }}" class="btn btn-outline-warning" title="edit()">Edit</a>
                                <form action="{{ route('product.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk via destroy()?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="destroy()">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data produk.</td>
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
