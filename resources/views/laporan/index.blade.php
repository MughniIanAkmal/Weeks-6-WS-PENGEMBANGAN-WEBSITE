@extends('layouts.app')

@section('title', 'Pusat Laporan & Ekspor PDF - POS Toko')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Pusat Laporan & Dokumen Bisnis
        </h3>
        <p class="text-muted small mb-0">
            Ekspor laporan resmi inventaris produk dan riwayat transaksi kasir ke dalam format PDF A4 rapi.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.products.pdf') }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak Katalog Produk
        </a>
        <a href="{{ route('reports.transactions.pdf') }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Rekap Transaksi
        </a>
    </div>
</div>

<!-- Ringkasan Statistik Eksekutif -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Total Produk</span>
                <h4 class="fw-bold my-1 text-dark">{{ number_format($productStats['total_items']) }} Item</h4>
                <div class="small text-muted">
                    <i class="bi bi-boxes me-1"></i>{{ number_format($productStats['total_stock']) }} unit stok fisik
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Nilai Aset Inventaris</span>
                <h4 class="fw-bold my-1 text-info">Rp {{ number_format($productStats['total_asset'], 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    <i class="bi bi-pie-chart me-1"></i>Total valuasi stok gudang
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Total Transaksi Kasir</span>
                <h4 class="fw-bold my-1 text-success">{{ number_format($transactionStats['total_trx']) }} Transaksi</h4>
                <div class="small text-muted">
                    <i class="bi bi-calendar-range me-1"></i>Periode aktif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
            <div class="card-body">
                <span class="text-muted small fw-semibold text-uppercase">Total Omzet Penjualan</span>
                <h4 class="fw-bold my-1 text-warning-emphasis">Rp {{ number_format($transactionStats['total_omzet'], 0, ',', '.') }}</h4>
                <div class="small text-muted">
                    <i class="bi bi-cash-stack me-1"></i>Pendapatan kotor POS
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Modul Ekspor PDF -->
<div class="row g-4 mb-4">
    <!-- Modul 1: Laporan Data Produk & Aset -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-box-seam text-primary fs-4 me-2"></i>
                <h5 class="fw-bold mb-0">1. Laporan Data Produk & Stok</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Menghasilkan dokumen resmi berisi seluruh daftar katalog produk, kode SKU, kategori, harga jual, jumlah stok fisik, dan kalkulasi nilai aset inventaris siap cetak.
                </p>
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Format Dokumen:</span>
                        <strong class="text-dark">PDF Standar A4 (Portrait)</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Diverifikasi oleh:</span>
                        <strong class="text-dark">Sistem DomPDF Barryvdh</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Status Stok Kritis:</span>
                        <span class="badge {{ $productStats['low_stock'] > 0 ? 'bg-danger' : 'bg-success' }}">
                            {{ $productStats['low_stock'] }} produk di bawah 10 unit
                        </span>
                    </div>
                </div>
                <div class="d-grid">
                    <a href="{{ route('reports.products.pdf') }}" target="_blank" class="btn btn-outline-primary fw-semibold">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Download / Cetak Laporan Produk PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modul 2: Laporan Transaksi Kasir dengan Filter -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-receipt text-success fs-4 me-2"></i>
                <h5 class="fw-bold mb-0">2. Laporan Penjualan Kasir POS</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Menghasilkan rekapitulasi data penjualan kasir lengkap dengan nomor invoice, kasir yang bertugas, nama pembeli, rincian diskon, pajak, dan total omzet.
                </p>

                <form action="{{ route('reports.transactions.pdf') }}" method="GET" target="_blank" class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Dari Tanggal:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-success w-100 fw-semibold">
                            <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Unduh Rekap Penjualan PDF (Landscape A4)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat 10 Transaksi Terakhir -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-clock-history me-1 text-secondary"></i> 10 Transaksi Penjualan Terakhir
            </h6>
            <small class="text-muted">Pratinjau transaksi kasir yang dapat langsung diekspor sebagai invoice individual</small>
        </div>
        <a href="{{ route('cashier.history') }}" class="btn btn-sm btn-outline-secondary">
            Lihat Semua Riwayat &rarr;
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Invoice</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th class="text-end">Total Belanja</th>
                    <th class="text-center">Aksi Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentTransactions as $t)
                    <tr>
                        <td class="fw-semibold"><code>{{ $t->invoice_number }}</code></td>
                        <td class="small text-muted">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->user->name ?? 'Kasir' }}</td>
                        <td>{{ $t->customer_name ?? 'Pelanggan Umum' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($t->payment_method) }}</span>
                        </td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reports.receipt.pdf', $t->id) }}" target="_blank" class="btn btn-outline-danger" title="Download Invoice PDF">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                                <a href="{{ route('cashier.receipt', $t->id) }}" class="btn btn-outline-secondary" title="Lihat Struk Kasir">
                                    <i class="bi bi-receipt"></i> Struk
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Belum ada transaksi penjualan yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
