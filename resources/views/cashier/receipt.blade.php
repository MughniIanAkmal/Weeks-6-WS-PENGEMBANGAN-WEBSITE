@extends('layouts.app')

@section('title', 'Struk Pembayaran #' . $transaction->invoice_number)

@section('content')
<style>
/* Styling khusus struk kasir thermal */
.receipt-wrapper {
    max-width: 420px;
    margin: 0 auto;
}

.receipt-container {
    max-width: 380px;
    margin: 0 auto;
    background: #fff;
    padding: 24px;
    border: 1px dashed #bbb;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    font-family: 'Courier New', Courier, monospace;
    font-size: 13px;
    color: #222;
}

.receipt-divider {
    border-top: 1px dashed #666;
    margin: 10px 0;
}

.receipt-double-divider {
    border-top: 2px dashed #333;
    margin: 12px 0;
}

/* Media Print: Hanya cetak struk */
@media print {
    @page {
        margin: 5mm;
        size: auto;
    }
    
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    nav, .navbar, .no-print, .alert, header, footer {
        display: none !important;
    }

    .container, .receipt-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .receipt-container {
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        max-width: 78mm !important;
        margin: 0 auto !important;
        padding: 4px !important;
    }
}
</style>

<div class="receipt-wrapper">
    <!-- Panel Tombol Aksi di Layar (Tidak ikut tercetak) -->
    <div class="card border-0 shadow-sm mb-3 no-print bg-white p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-success px-2 py-1">✓ Transaksi Sukses</span>
            <span class="small text-muted">{{ $transaction->invoice_number }}</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary fw-bold flex-grow-1 py-2" onclick="triggerPrint()">
                🖨️ Cetak Struk (Print)
            </button>
            <a href="{{ route('cashier.index') }}" class="btn btn-outline-secondary fw-semibold">
                + Kasir Baru
            </a>
            <a href="{{ route('cashier.history') }}" class="btn btn-outline-dark">
                Riwayat
            </a>
        </div>
        <div class="text-center mt-2">
            <small class="text-muted">Dialog cetak muncul otomatis. Jika tidak muncul, klik tombol biru di atas atau tekan <strong>Ctrl + P</strong>.</small>
        </div>
    </div>

    <!-- Box Struk Belanja Kasir -->
    <div class="receipt-container" id="printableReceipt">
        <!-- Header Toko -->
        <div class="text-center">
            <h5 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">POS TOKO</h5>
            <p class="mb-0 small">Workshop Web Framework</p>
            <p class="mb-0 small">Politeknik Negeri Jember</p>
            <p class="mb-0 small">Telp: (0331) 333532</p>
        </div>

        <div class="receipt-double-divider"></div>

        <!-- Meta Transaksi -->
        <div>
            <div class="d-flex justify-content-between">
                <span>No. Invoice:</span>
                <strong class="text-end">{{ $transaction->invoice_number }}</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span>Waktu:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Kasir:</span>
                <span>{{ $transaction->user->name ?? 'Kasir' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Pelanggan:</span>
                <span>{{ $transaction->customer_name }}</span>
            </div>
            @if ($transaction->member_number)
                <div class="d-flex justify-content-between">
                    <span>No. Member:</span>
                    <span>{{ $transaction->member_number }}</span>
                </div>
            @endif
        </div>

        <div class="receipt-divider"></div>

        <!-- Daftar Item Belanja -->
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px dashed #888;">
                    <th style="text-align: left; padding-bottom: 4px;">Item</th>
                    <th style="text-align: center; padding-bottom: 4px;">Qty</th>
                    <th style="text-align: right; padding-bottom: 4px;">Harga</th>
                    <th style="text-align: right; padding-bottom: 4px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td colspan="4" style="padding-top: 6px; font-weight: bold;">
                            {{ $item->product_name }}
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px dotted #ccc;">
                        <td style="padding-bottom: 4px;"></td>
                        <td style="text-align: center; padding-bottom: 4px;">{{ $item->quantity }}</td>
                        <td style="text-align: right; padding-bottom: 4px;">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: right; padding-bottom: 4px; font-weight: bold;">
                            {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Rincian Pembayaran (Sesuai Modul Hal. 21) -->
        <div style="margin-top: 8px;">
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            @if ($transaction->discount > 0)
                <div class="d-flex justify-content-between text-danger">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ($transaction->tax > 0)
                <div class="d-flex justify-content-between">
                    <span>PPN:</span>
                    <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ($transaction->other_fee > 0)
                <div class="d-flex justify-content-between">
                    <span>Biaya Lain:</span>
                    <span>Rp {{ number_format($transaction->other_fee, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="receipt-divider"></div>

            <div class="d-flex justify-content-between fw-bold" style="font-size: 15px;">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <span>Metode:</span>
                <span class="text-uppercase">{{ $transaction->payment_method }}</span>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <span>Bayar:</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mt-1 fw-bold">
                <span>Kembali:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-double-divider"></div>

        <!-- Footer Struk -->
        <div class="text-center small mt-3">
            <p class="mb-1 fw-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
            <p class="mb-0 text-muted" style="font-size: 11px;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
            <p class="mb-0 text-muted" style="font-size: 10px;">Layanan Konsumen: support@postoko.local</p>
        </div>
    </div>

    <!-- Tombol Cetak Bawah -->
    <div class="text-center mt-3 no-print">
        <button type="button" class="btn btn-success btn-lg px-4 shadow-sm fw-bold" onclick="triggerPrint()">
            🖨️ Cetak Struk Sekarang
        </button>
    </div>
</div>

<script>
function triggerPrint() {
    window.print();
}
</script>
@endsection
