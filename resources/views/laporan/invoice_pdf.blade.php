<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 10px; }
        .invoice-box { padding: 10px; }
        .header { border-bottom: 2px solid #0284c7; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; color: #0284c7; margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background: #f1f5f9; padding: 6px; text-align: left; font-size: 10px; border-bottom: 1px solid #cbd5e1; }
        .table td { padding: 6px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        .text-right { text-align: right; }
        .total-row { font-size: 12px; font-weight: bold; color: #0284c7; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h2 class="title">INVOICE PENJUALAN</h2>
            <div style="font-size: 10px; color: #64748b;">POS Toko Indonesia &middot; Struk Resmi Pembelian</div>
        </div>

        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 50%;">
                    <strong>No. Invoice:</strong> #{{ $transaction->invoice_number }}<br>
                    <strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Kasir:</strong> {{ $transaction->user->name ?? 'Kasir' }}
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <strong>Pelanggan:</strong> {{ $transaction->customer_name ?? 'Pelanggan Umum' }}<br>
                    <strong>Pembayaran:</strong> {{ strtoupper($transaction->payment_method) }}<br>
                    <strong>Status:</strong> {{ strtoupper($transaction->status) }}
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Item Produk</th>
                    <th class="text-right" style="width: 40px;">Qty</th>
                    <th class="text-right" style="width: 80px;">Harga</th>
                    <th class="text-right" style="width: 90px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">Subtotal:</td>
                    <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if ($transaction->discount > 0)
                    <tr>
                        <td colspan="3" class="text-right" style="color: red;">Diskon:</td>
                        <td class="text-right" style="color: red;">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL AKHIR:</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Dibayar:</td>
                    <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right">Kembalian:</td>
                    <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="text-align: center; margin-top: 25px; font-size: 9px; color: #94a3b8;">
            Terima kasih atas kunjungan Anda di POS Toko Indonesia!
        </div>
    </div>
</body>
</html>
