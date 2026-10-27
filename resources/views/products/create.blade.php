@extends('layouts.app')

@section('title', 'Tambah Produk Baru - POS Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('product.index') }}" class="text-secondary text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Kembali ke Manajemen Produk
                </a>
                <h4 class="fw-bold mt-1 mb-0 text-dark">Tambah Produk Baru</h4>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <!-- Kategori Produk -->
                    <div class="col-md-6">
                        <label for="category_id" class="form-label small fw-semibold">Kategori Produk <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Contoh: BRG-001" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nama Produk -->
                    <div class="col-12">
                        <label for="name" class="form-label small fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama barang lengkap..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Harga Produk -->
                    <div class="col-md-6">
                        <label for="price" class="form-label small fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" min="0" placeholder="0" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stok Awal -->
                    <div class="col-md-6">
                        <label for="stock" class="form-label small fw-semibold">Jumlah Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 10) }}" min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload Foto Produk (Acara 29 File Storage & Acara 31 Intervention Image v3) -->
                    <div class="col-12">
                        <label for="image" class="form-label small fw-semibold">Foto Produk</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
                        <div class="form-text small text-muted">
                            Format yang didukung: JPG, PNG, WebP (Maks. 2MB). Foto akan otomatis disimpan ke disk storage dan dibuatkan thumbnail 300x200 pixel.
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <!-- Preview Gambar -->
                        <div id="imagePreviewContainer" class="mt-3 d-none">
                            <span class="small text-muted d-block mb-1">Pratinjau Foto Baru:</span>
                            <img id="imagePreview" src="#" alt="Pratinjau Foto" class="rounded shadow-sm border" style="max-width: 200px; max-height: 140px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('product.index') }}" class="btn btn-outline-secondary btn-sm px-3">Batal</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Produk
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
