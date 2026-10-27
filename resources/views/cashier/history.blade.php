@extends('layouts.app')

@section('title', 'Riwayat Transaksi Kasir - POS Toko')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Riwayat Transaksi Kasir</h4>
        <small class="text-muted">Daftar rekapan transaksi penjualan dan cetak ulang struk</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.transactions.pdf') }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh Laporan PDF
        </a>
        <a href="{{ route('cashier.index') }}" class="btn btn-primary btn-sm">
            + Buka Mesin Kasir (POS)
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
            Kembali ke Katalog
        </a>
    </div>
</div>

<div class="card shadow-sm border">
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>No. Invoice</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th class="text-center">Total Belanja</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $index => $trx)
                    <tr>
                        <td class="text-center text-muted small">{{ $transactions->firstItem() + $index }}</td>
                        <td>
                            <code>{{ $trx->invoice_number }}</code>
                        </td>
                        <td class="small text-muted">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $trx->user->name ?? 'Kasir' }}</span>
                        </td>
                        <td>{{ $trx->customer_name }}</td>
                        <td class="text-center fw-bold text-success">
                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-dark border small text-uppercase">
                                {{ $trx->payment_method }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('cashier.receipt', $trx->id) }}" class="btn btn-outline-primary py-0 px-2" title="Cetak Struk Thermal">
                                    <i class="bi bi-receipt"></i> Struk
                                </a>
                                <a href="{{ route('reports.receipt.pdf', $trx->id) }}" target="_blank" class="btn btn-outline-danger py-0 px-2" title="Unduh PDF Resmi">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Belum ada riwayat transaksi yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($transactions->hasPages())
        <div class="card-footer bg-white py-2">
            <div class="d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
