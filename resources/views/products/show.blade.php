@extends('layouts.app')

@section('title', 'Detail Produk - ACARA 13')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <a href="{{ route('product.index') }}" class="text-secondary small">&larr; Kembali ke Daftar Produk</a>
        <h5 class="fw-bold mt-2 mb-3">Detail Produk (ProductController::show)</h5>

        <div class="card p-3 shadow-sm">
            <table class="table table-bordered mb-3">
                <tr>
                    <th style="width: 140px;">ID Produk</th>
                    <td>{{ $product->id }}</td>
                </tr>
                <tr>
                    <th>Nama Produk</th>
                    <td class="fw-bold">{{ $product->name }}</td>
                </tr>
                <tr>
                    <th>SKU</th>
                    <td><code>{{ $product->sku }}</code></td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $product->category->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Stok</th>
                    <td>{{ $product->stock }} unit</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $product->created_at }}</td>
                </tr>
            </table>

            <div class="d-flex justify-content-between">
                <a href="{{ route('product.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit Produk</a>
            </div>
        </div>
    </div>
</div>
@endsection
