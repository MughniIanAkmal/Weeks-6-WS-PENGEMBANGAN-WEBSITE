<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Halaman Utama Pusat Laporan & Statistik POS Toko.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        // Statistik Produk
        $productStats = [
            'total_items' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'total_asset' => Product::select(DB::raw('SUM(price * stock) as total_asset'))->value('total_asset') ?? 0,
            'low_stock'   => Product::where('stock', '<', 10)->count(),
        ];

        // Statistik Transaksi Penjualan
        $transactionQuery = Transaction::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $transactionStats = [
            'total_trx'     => (clone $transactionQuery)->count(),
            'total_omzet'   => (clone $transactionQuery)->sum('total_amount'),
            'total_discount'=> (clone $transactionQuery)->sum('discount'),
            'total_tax'     => (clone $transactionQuery)->sum('tax'),
        ];

        $recentTransactions = Transaction::with(['user', 'items'])->latest()->take(10)->get();

        return view('laporan.index', compact(
            'productStats',
            'transactionStats',
            'recentTransactions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Cetak Laporan PDF Data Produk & Nilai Inventaris (Integrasi Acara 30 - DomPDF).
     */
    public function exportProdukPdf()
    {
        $products = Product::with('category')->orderBy('category_id')->orderBy('name')->get();

        $totalStock = $products->sum('stock');
        $totalAssetValue = $products->sum(function ($item) {
            return $item->price * $item->stock;
        });

        $generatedAt = now()->translatedFormat('d F Y, H:i:s');

        $pdf = Pdf::loadView('laporan.produk_pdf', compact('products', 'totalStock', 'totalAssetValue', 'generatedAt'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->stream('laporan_data_produk_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Cetak Laporan PDF Riwayat Transaksi Penjualan Kasir (Integrasi Acara 30 - DomPDF).
     */
    public function exportTransaksiPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Transaction::with(['user', 'items'])->latest('created_at');

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
            $periode = date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate));
        } else {
            $periode = 'Semua Riwayat Transaksi';
        }

        $transactions = $query->get();

        $totalOmzet = $transactions->sum('total_amount');
        $totalDiskon = $transactions->sum('discount');
        $totalPajak = $transactions->sum('tax');
        $totalItemTerjual = $transactions->sum(function ($trx) {
            return $trx->items->sum('quantity');
        });

        $generatedAt = now()->translatedFormat('d F Y, H:i:s');

        $pdf = Pdf::loadView('laporan.transaksi_pdf', compact(
            'transactions',
            'totalOmzet',
            'totalDiskon',
            'totalPajak',
            'totalItemTerjual',
            'periode',
            'generatedAt'
        ))->setPaper('a4', 'landscape')
          ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->stream('laporan_penjualan_pos_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Cetak Invoice / Struk Resmi Transaksi Tunggal format PDF.
     */
    public function exportSingleTransactionPdf($id)
    {
        $transaction = Transaction::with(['user', 'items'])->findOrFail($id);

        $pdf = Pdf::loadView('laporan.invoice_pdf', compact('transaction'))
            ->setPaper('a5', 'portrait')
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->stream('invoice_' . $transaction->invoice_number . '.pdf');
    }
}
