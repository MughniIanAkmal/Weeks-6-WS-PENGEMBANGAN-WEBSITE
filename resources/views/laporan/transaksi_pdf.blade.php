<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Penjualan Kasir POS</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333333;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #064e3b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 2px 0 0 0;
            color: #64748b;
            font-size: 10px;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 12px;
        }
        .meta-info td {
            font-size: 10px;
            padding: 2px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .report-table th {
            background-color: #059669;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 6px 4px;
            border: 1px solid #059669;
            text-align: left;
        }
        .report-table td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            font-size: 9.5px;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .badge-success { background-color: #dcfce7; color: #16a34a; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>POS TOKO INDONESIA</h1>
        <p>Jl. Mastrip No. 164, Jember &middot; Sistem Kasir Point of Sale (POS) Terpadu</p>
        <p><strong>REKAPITULASI TRANSAKSI PENJUALAN KASIR</strong></p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 50%;"><strong>Periode Laporan:</strong> {{ $periode }}</td>
            <td style="text-align: right;"><strong>Total Transaksi:</strong> {{ $transactions->count() }} Transaksi</td>
        </tr>
        <tr>
            <td><strong>Waktu Cetak:</strong> {{ $generatedAt }}</td>
            <td style="text-align: right;"><strong>Total Item Terjual:</strong> {{ number_format($totalItemTerjual, 0, ',', '.') }} Qty</td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 105px;">No. Invoice</th>
                <th style="width: 85px;">Waktu & Tanggal</th>
                <th style="width: 85px;">Kasir</th>
                <th style="width: 100px;">Pelanggan</th>
                <th style="width: 55px;" class="text-center">Metode</th>
                <th style="width: 75px;" class="text-right">Subtotal</th>
                <th style="width: 60px;" class="text-right">Diskon</th>
                <th style="width: 55px;" class="text-right">Pajak</th>
                <th style="width: 85px;" class="text-right">Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $trx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold"><code>{{ $trx->invoice_number }}</code></td>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $trx->user->name ?? 'Kasir' }}</td>
                    <td>{{ $trx->customer_name ?? 'Pelanggan Umum' }}</td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ strtoupper($trx->payment_method) }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #dc2626;">
                        {{ $trx->discount > 0 ? '-Rp ' . number_format($trx->discount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $trx->tax > 0 ? 'Rp ' . number_format($trx->tax, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right fw-bold" style="color: #065f46;">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">Tidak ada riwayat transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #ecfdf5; font-weight: bold; font-size: 10px;">
                <td colspan="6" class="text-right" style="padding: 6px;">TOTAL KESELURUHAN OMZET:</td>
                <td class="text-right">Rp {{ number_format($transactions->sum('subtotal'), 0, ',', '.') }}</td>
                <td class="text-right" style="color: #dc2626;">-Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                <td class="text-right">+Rp {{ number_format($totalPajak, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #047857; font-size: 11px;">
                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Diperiksa oleh,<br>
                <strong>Kepala Keuangan Toko</strong>
                <br><br><br>
                ( _________________________ )
            </td>
            <td>
                Jember, {{ date('d F Y') }}<br>
                <strong>Admin Kasir Bertugas</strong>
                <br><br><br>
                ( _________________________ )
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh POS Toko Indonesia &middot; Halaman Dokumen Kasir Terverifikasi
    </div>
</body>
</html>
