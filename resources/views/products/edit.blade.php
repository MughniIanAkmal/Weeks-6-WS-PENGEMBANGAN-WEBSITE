@extends('layouts.app')

@section('title', 'Edit Produk - ' . $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('product.index') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Produk
                </a>
                <h4 class="fw-bold mt-1 mb-0 text-dark">Edit Produk: {{ $product->name }}</h4>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Kategori Produk -->
                    <div class="col-md-6">
                        <label for="category_id" class="form-label small fw-semibold">Kategori Produk <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SKU Produk -->
                    <div class="col-md-6">
                        <label for="sku" class="form-label small fw-semibold">Kode SKU / Barcode <span class="text-danger">*</span></label>
                        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Produk -->
                    <div class="col-12">
                        <label for="name" class="form-label small fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Harga Produk -->
                    <div class="col-md-6">
                        <label for="price" class="form-label small fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" min="0" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stok Produk -->
                    <div class="col-md-6">
                        <label for="stock" class="form-label small fw-semibold">Jumlah Stok Fisik <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Foto Saat Ini & Upload Foto Baru -->
                    <div class="col-12">
                        <label for="image" class="form-label small fw-semibold">Ganti Foto Produk</label>

                        @if ($product->thumbnail_url)
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-2 border">
                                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="rounded shadow-sm" style="width: 80px; height: 60px; object-fit: cover;">
                                <div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">Foto Aktif Tersimpan</span>
                                    <div class="small text-muted">Unggah file baru di bawah ini jika ingin mengganti foto saat ini.</div>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
                        <div class="form-text small text-muted">
                            Biarkan kosong jika tidak ingin mengubah foto produk. Sistem otomatis membuat ulang thumbnail 300x200 jika diisi.
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Preview Gambar Baru -->
                        <div id="imagePreviewContainer" class="mt-3 d-none">
                            <span class="small text-muted d-block mb-1">Pratinjau Foto Baru:</span>
                            <img id="imagePreview" src="#" alt="Pratinjau Foto Baru" class="rounded shadow-sm border" style="max-width: 200px; max-height: 140px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('product.index') }}" class="btn btn-outline-secondary btn-sm px-3">Batal</a>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-semibold text-dark">
                        <i class="bi bi-check2-circle me-1"></i> Perbarui Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const container = document.getElementById('imagePreviewContainer');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            container.classList.add('d-none');
        }
    }
</script>
@endpush
@endsection
