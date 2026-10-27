<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Produk & Inventaris Toko</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #64748b;
            font-size: 10px;
        }
        .meta-info {
            margin-bottom: 15px;
            width: 100%;
        }
        .meta-info td {
            font-size: 10px;
            padding: 2px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 6px;
            border: 1px solid #0284c7;
            text-align: left;
        }
        .report-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-danger { background-color: #fee2e2; color: #dc2626; }
        .badge-success { background-color: #dcfce7; color: #16a34a; }
        .total-box {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .total-box td {
            padding: 6px 12px;
            font-size: 11px;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 11px;
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
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header / Kop Surat -->
    <div class="header">
        <h1>POS TOKO INDONESIA</h1>
        <p>Jl. Mastrip No. 164, Kotak Pos 164, Jember - Jawa Timur | Telp: (0331) 333532</p>
        <p><strong>LAPORAN RESMI DATA KATALOG & NILAI INVENTARIS PRODUK</strong></p>
    </div>

    <!-- Informasi Cetak -->
    <table class="meta-info">
        <tr>
            <td style="width: 50%;"><strong>Dicetak Pada:</strong> {{ $generatedAt }}</td>
            <td style="text-align: right;"><strong>Total Jenis Produk:</strong> {{ $products->count() }} Item</td>
        </tr>
        <tr>
            <td><strong>Status Dokumen:</strong> Sah / Terverifikasi Sistem POS</td>
            <td style="text-align: right;"><strong>Total Unit Stok:</strong> {{ number_format($totalStock, 0, ',', '.') }} Pcs</td>
        </tr>
    </table>

    <!-- Tabel Daftar Produk -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 70px;">Kode SKU</th>
                <th>Nama Produk</th>
                <th style="width: 110px;">Kategori</th>
                <th style="width: 80px;" class="text-right">Harga Satuan</th>
                <th style="width: 45px;" class="text-center">Stok</th>
                <th style="width: 100px;" class="text-right">Nilai Aset Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $index => $item)
                @php
                    $assetVal = $item->price * $item->stock;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><code>{{ $item->sku }}</code></td>
                    <td class="fw-bold">{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->stock < 10 ? 'badge-danger' : 'badge-success' }}">
                            {{ $item->stock }}
                        </span>
                    </td>
                    <td class="text-right fw-bold">Rp {{ number_format($assetVal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Belum ada data produk tersimpan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 8px;">TOTAL KESELURUHAN ASET INVENTARIS:</td>
                <td class="text-center" style="padding: 8px;">{{ number_format($totalStock, 0, ',', '.') }}</td>
                <td class="text-right" style="padding: 8px; color: #0284c7; font-size: 11px;">
                    Rp {{ number_format($totalAssetValue, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Supervisor Operasional</strong>
                <br><br><br><br>
                ( _________________________ )
            </td>
            <td>
                Jember, {{ date('d F Y') }}<br>
                <strong>Manajer Toko & Inventaris</strong>
                <br><br><br><br>
                ( _________________________ )
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh Sistem Point of Sale (POS) Toko &middot; Halaman 1
    </div>
</body>
</html>
