@extends('layouts.app')

@section('title', 'Manajemen Berkas & Media - POS Toko')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-folder2-open text-primary me-2"></i>Manajemen Berkas & Media Toko
        </h3>
        <p class="text-muted small mb-0">
            Kelola berkas publik sistem POS Toko, brosur, dokumen faktur, banner promosi, dan aset media terintegrasi.
        </p>
    </div>
    <div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
            <i class="bi bi-hdd-network me-1"></i> Public Storage Terhubung
        </span>
    </div>
</div>

<!-- Statistik Media Storage -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Jumlah Berkas Tersimpan</span>
                    <h4 class="fw-bold my-1 text-dark">{{ $stats['total_files'] }} File</h4>
                    <small class="text-muted">Tersimpan di direktori <code>storage/app/public/uploads</code></small>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                    <i class="bi bi-files"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-info border-4">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Penggunaan Ruang</span>
                    <h4 class="fw-bold my-1 text-info">{{ $stats['total_usage'] }}</h4>
                    <small class="text-muted">Kapasitas penyimpanan disk lokal</small>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Form Unggah Berkas Baru -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-cloud-arrow-up text-primary me-1"></i> Unggah Berkas Baru
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label small fw-semibold">Pilih Berkas / Media</label>
                        <input class="form-control form-control-sm" type="file" id="file" name="file" required>
                        <div class="form-text small text-muted mt-2">
                            Maksimal 10 MB. Mendukung format gambar (JPG, PNG, WebP), dokumen PDF, Excel, Word, dan arsip ZIP.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-sm py-2 fw-semibold">
                        <i class="bi bi-upload me-1"></i> Unggah ke Server
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3 bg-light">
            <div class="card-body small text-muted">
                <strong class="text-dark"><i class="bi bi-info-circle me-1"></i> Catatan Integrasi Media:</strong>
                <p class="mb-0 mt-1">
                    Semua berkas yang diunggah secara otomatis disimpan di storage publik Laravel yang dapat diakses secara instan oleh modul POS Kasir dan Katalog Produk.
                </p>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Berkas & Media -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-collection me-1 text-secondary"></i> Berkas & Aset Tersimpan
                </h6>
                <span class="badge bg-secondary">{{ count($files) }} Berkas</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">Pratinjau</th>
                            <th>Nama Berkas</th>
                            <th>Ukuran</th>
                            <th>Diunggah Pada</th>
                            <th style="width: 130px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($files as $file)
                            <tr>
                                <td class="text-center">
                                    @if ($file['is_image'])
                                        <a href="{{ $file['url'] }}" target="_blank">
                                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="rounded shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                                        </a>
                                    @else
                                        <div class="bg-light text-secondary rounded d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="bi bi-file-earmark-text fs-4"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 240px;" title="{{ $file['name'] }}">
                                        {{ $file['name'] }}
                                    </div>
                                    <small class="text-muted">{{ $file['mime_type'] }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $file['size'] }}</span></td>
                                <td class="small text-muted">{{ $file['last_modified'] }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('media.download', $file['name']) }}" class="btn btn-outline-primary" title="Unduh Berkas">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('media.destroy', $file['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berkas ini dari server?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Berkas">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada berkas atau dokumen yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
