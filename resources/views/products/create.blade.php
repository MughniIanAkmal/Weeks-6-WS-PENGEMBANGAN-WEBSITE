@extends('layouts.app')

@section('title', 'Tambah Produk - ACARA 13')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <a href="{{ route('product.index') }}" class="text-secondary small">&larr; Kembali ke Daftar Produk</a>
        <h5 class="fw-bold mt-2 mb-3">Tambah Produk Baru (ProductController::create)</h5>

        <div class="card p-3 shadow-sm">
            <form action="{{ route('product.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Produk</label>
                    <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{ old('name') }}" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" id="sku" class="form-control form-control-sm" value="{{ old('sku') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Harga (Rp)</label>
                        <input type="number" name="price" id="price" class="form-control form-control-sm" value="{{ old('price') }}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" name="stock" id="stock" class="form-control form-control-sm" value="{{ old('stock', 10) }}" min="0" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Simpan Produk (store)</button>
            </form>
        </div>
    </div>
</div>
@endsection
