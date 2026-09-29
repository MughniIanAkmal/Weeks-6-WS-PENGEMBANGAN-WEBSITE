@extends('layouts.app')

@section('title', 'Edit Produk - ACARA 13')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <a href="{{ route('product.index') }}" class="text-secondary small">&larr; Kembali ke Daftar Produk</a>
        <h5 class="fw-bold mt-2 mb-3">Edit Produk (ProductController::edit)</h5>

        <div class="card p-3 shadow-sm">
            <form action="{{ route('product.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Produk</label>
                    <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" id="sku" class="form-control form-control-sm" value="{{ old('sku', $product->sku) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Harga (Rp)</label>
                        <input type="number" name="price" id="price" class="form-control form-control-sm" value="{{ old('price', $product->price) }}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" name="stock" id="stock" class="form-control form-control-sm" value="{{ old('stock', $product->stock) }}" min="0" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-sm">Perbarui Produk (update)</button>
            </form>
        </div>
    </div>
</div>
@endsection
