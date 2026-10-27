@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('product.index') }}" class="text-secondary text-decoration-none small">
                <i class="bi bi-arrow-left"></i> Kembali ke Katalog Produk
            </a>
            <h4 class="fw-bold mt-1 mb-0 text-dark">Informasi Detail Produk</h4>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-5 text-center">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded-3 shadow-sm border" style="max-height: 240px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-3 d-flex flex-column align-items-center justify-content-center py-5 border">
                            <i class="bi bi-image fs-1 text-secondary opacity-50"></i>
                            <span class="small text-muted mt-2">Tidak ada foto produk</span>
                        </div>
                    @endif
                </div>

                <div class="col-md-7">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">
                        {{ $product->category->name ?? 'Kategori Umum' }}
                    </span>
                    <h4 class="fw-bold text-dark mb-1">{{ $product->name }}</h4>
                    <p class="text-muted small mb-3">Kode SKU: <code>{{ $product->sku }}</code></p>

                    <h3 class="fw-bold text-primary mb-3">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </h3>

                    <table class="table table-sm table-borderless small mb-4">
                        <tr>
                            <td style="width: 130px;" class="text-muted">Ketersediaan Stok:</td>
                            <td>
                                <span class="badge {{ $product->stock <= 0 ? 'bg-danger' : ($product->stock < 10 ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $product->stock }} Unit Tersedia
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Ditambahkan:</td>
                            <td class="text-dark">{{ $product->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir Diperbarui:</td>
                            <td class="text-dark">{{ $product->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2">
                        @can('update', $product)
                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-warning btn-sm px-3 text-dark fw-semibold">
                                <i class="bi bi-pencil me-1"></i> Edit Data Produk
                            </a>
                        @endcan
                        <a href="{{ route('product.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                            Tutup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
