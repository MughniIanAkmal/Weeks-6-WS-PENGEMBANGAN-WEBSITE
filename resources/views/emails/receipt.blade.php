<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Belanja - POS Toko</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .receipt-card { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .header { text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 16px; margin-bottom: 16px; }
        .header h2 { margin: 0 0 4px 0; color: #0f172a; }
        .meta-table, .items-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 4px 0; font-size: 13px; }
        .items-table { margin-top: 16px; border-top: 1px solid #e2e8f0; }
        .items-table th { text-align: left; background: #f1f5f9; padding: 8px; font-size: 12px; text-transform: uppercase; color: #475569; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .text-right { text-align: right; }
        .summary-table { width: 100%; margin-top: 16px; border-top: 2px dashed #cbd5e1; padding-top: 12px; }
        .summary-table td { padding: 4px 0; font-size: 14px; }
        .grand-total { font-size: 16px; font-weight: bold; color: #0284c7; }
        .footer { text-align: center; margin-top: 24px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="header">
            <h2>POS TOKO INDONESIA</h2>
            <p style="margin: 0; font-size: 13px; color: #64748b;">Sistem Point of Sale Modern Terintegrasi</p>
            <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">Terima kasih telah berbelanja di toko kami!</p>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 50%;"><strong>No. Invoice:</strong> #{{ $transaction->invoice_number }}</td>
                <td class="text-right"><strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Pelanggan:</strong> {{ $transaction->customer_name ?? 'Pelanggan Umum' }}</td>
                <td class="text-right"><strong>Kasir:</strong> {{ $transaction->user->name ?? 'Kasir Utama' }}</td>
            </tr>
            <tr>
                <td><strong>Metode Bayar:</strong> {{ strtoupper($transaction->payment_method) }}</td>
                <td class="text-right"><strong>Status:</strong> {{ strtoupper($transaction->status) }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Produk</th>
                    <th class="text-right" style="width: 50px;">Qty</th>
                    <th class="text-right" style="width: 100px;">Harga</th>
                    <th class="text-right" style="width: 110px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if ($item->product_code)
                                <br><small style="color: #64748b;">SKU: {{ $item->product_code }}</small>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if ($transaction->discount > 0)
                <tr>
                    <td style="color: #dc2626;">Diskon</td>
                    <td class="text-right" style="color: #dc2626;">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if ($transaction->tax > 0)
                <tr>
                    <td>Pajak (PPN)</td>
                    <td class="text-right">+ Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if ($transaction->other_fee > 0)
                <tr>
                    <td>Biaya Lainnya</td>
                    <td class="text-right">+ Rp {{ number_format($transaction->other_fee, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>TOTAL PEMBAYARAN</td>
                <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunai / Dibayar</td>
                <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        @if ($transaction->notes)
            <div style="margin-top: 12px; padding: 8px; background: #f8fafc; border-radius: 4px; font-size: 12px; color: #64748b;">
                <strong>Catatan:</strong> {{ $transaction->notes }}
            </div>
        @endif

        <div class="footer">
            <p style="margin: 0;">Barang yang sudah dibeli tidak dapat ditukar kembali kecuali ada perjanjian.</p>
            <p style="margin: 4px 0 0 0; color: #94a3b8;">Email ini digenerate secara otomatis oleh Sistem POS Toko.</p>
        </div>
    </div>
</body>
</html>
