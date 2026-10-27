<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    /**
     * Tampilan Utama POS Kasir (Sesuai Desain & Fitur Prosedur Kerja Praktikum Ke-3).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $query = Product::with('category')->where('stock', '>', 0);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Data JSON produk aktif untuk autocomplete, barcode scanner & pencarian instan (Modul Hal. 11-12)
        $allProducts = Product::where('stock', '>', 0)->get()->map(function ($p) {
            return [
                'id'      => $p->id,
                'code'    => $p->sku,
                'barcode' => $p->sku,
                'name'    => $p->name,
                'price'   => (float) $p->price,
                'stock'   => $p->stock,
            ];
        });

        // Generate No. Transaksi otomatis sesuai tanggal (Modul Hal. 3)
        $todayCount = Transaction::whereDate('created_at', today())->count() + 1;
        $transactionNumber = 'TRX-' . date('Ymd') . '-' . sprintf('%03d', $todayCount);

        // Ambil keranjang session jika ada
        $cart = session()->get('cart', []);
        $cartTotal = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        // Daftar transaksi yang ditahan (Modul Hal. 22)
        $heldTransactions = session()->get('held_transactions', []);

        return view('cashier.index', compact(
            'products',
            'categories',
            'allProducts',
            'transactionNumber',
            'cart',
            'cartTotal',
            'totalItems',
            'heldTransactions',
            'search',
            'categoryId'
        ));
    }

    /**
     * Menambahkan item ke keranjang belanja (Server-side & barcode scanner fallback).
     */
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->stock <= 0) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Stok produk habis!"], 400);
            }
            return redirect()->back()->with('error', "Stok produk '{$product->name}' habis!");
        }

        $cart = session()->get('cart', []);
        $quantity = (int) $request->input('quantity', 1);

        if (isset($cart[$id])) {
            $newQuantity = $cart[$id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => "Kuantitas melebihi stok!"], 400);
                }
                return redirect()->back()->with('error', "Kuantitas melebihi stok yang tersedia (Maks: {$product->stock})!");
            }

            $cart[$id]['quantity'] = $newQuantity;
        } else {
            if ($quantity > $product->stock) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => "Kuantitas melebihi stok!"], 400);
                }
                return redirect()->back()->with('error', "Kuantitas melebihi stok yang tersedia (Maks: {$product->stock})!");
            }

            $cart[$id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'sku'      => $product->sku,
                'code'     => $product->sku,
                'price'    => (float) $product->price,
                'quantity' => $quantity,
                'stock'    => $product->stock,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'cart' => $cart]);
        }

        return redirect()->back()->with('success', "Produk '{$product->name}' ditambahkan ke keranjang!");
    }

    /**
     * Memperbarui kuantitas produk di keranjang.
     */
    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $product = Product::findOrFail($id);
        $action = $request->input('action');

        if (isset($cart[$id])) {
            if ($action === 'plus') {
                if ($cart[$id]['quantity'] + 1 > $product->stock) {
                    return redirect()->back()->with('error', "Stok maksimal tercapai ({$product->stock})!");
                }
                $cart[$id]['quantity'] += 1;
            } elseif ($action === 'minus') {
                $cart[$id]['quantity'] -= 1;
                if ($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
            } elseif ($action === 'set') {
                $qty = (int) $request->input('quantity', 1);
                if ($qty <= 0) {
                    unset($cart[$id]);
                } elseif ($qty > $product->stock) {
                    return redirect()->back()->with('error', "Kuantitas melebihi stok ({$product->stock})!");
                } else {
                    $cart[$id]['quantity'] = $qty;
                }
            }

            session()->put('cart', $cart);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'cart' => $cart]);
        }

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Menghapus item dari keranjang belanja.
     */
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $name = $cart[$id]['name'];
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('info', "Produk '{$name}' telah dihapus dari keranjang.");
        }

        return redirect()->back();
    }

    /**
     * Mengosongkan seluruh keranjang belanja (Batal Transaksi / Reset).
     */
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back()->with('info', 'Keranjang belanja berhasil dikosongkan.');
    }

    /**
     * Tahan Transaksi (Parkir Transaksi - Modul Hal. 22).
     */
    public function holdTransaction(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong, tidak ada transaksi untuk ditahan.'], 400);
        }

        $heldTransactions = session()->get('held_transactions', []);
        $holdId = 'HOLD-' . strtoupper(Str::random(4));

        $heldTransactions[$holdId] = [
            'id'            => $holdId,
            'time'          => now()->format('H:i'),
            'customer_name' => $request->input('customer_name', 'Pelanggan Umum'),
            'cart'          => $cart,
            'total_items'   => array_sum(array_column($cart, 'quantity')),
            'total_amount'  => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)),
        ];

        session()->put('held_transactions', $heldTransactions);
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => "Transaksi [{$holdId}] berhasil ditahan.",
            'held'    => $heldTransactions,
        ]);
    }

    /**
     * Lanjutkan kembali transaksi yang ditahan.
     */
    public function resumeTransaction($holdId)
    {
        $heldTransactions = session()->get('held_transactions', []);

        if (isset($heldTransactions[$holdId])) {
            session()->put('cart', $heldTransactions[$holdId]['cart']);
            unset($heldTransactions[$holdId]);
            session()->put('held_transactions', $heldTransactions);

            return redirect()->route('cashier.index')->with('success', "Transaksi [{$holdId}] berhasil dipulihkan ke keranjang.");
        }

        return redirect()->route('cashier.index')->with('error', 'Transaksi yang ditahan tidak ditemukan.');
    }

    /**
     * Memproses Checkout transaksi penjualan kasir (Modul Hal. 18 & 20-21).
     */
    public function checkout(Request $request)
    {
        // Bisa menerima cart dari session atau dari payload JSON
        $cart = $request->has('cart') && is_array($request->cart) ? $request->cart : session()->get('cart', []);

        if (empty($cart)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Keranjang masih kosong.'], 400);
            }
            return redirect()->back()->with('error', 'Keranjang belanja masih kosong!');
        }

        $request->validate([
            'customer_name'    => 'nullable|string|max:100',
            'member_number'    => 'nullable|string|max:50',
            'payment_method'   => 'required|in:cash,qris,debit,kredit,ewallet,transfer',
            'paid_amount'      => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount'  => 'nullable|numeric|min:0',
            'tax'              => 'nullable|numeric|min:0',
            'other_fee'        => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:255',
        ]);

        // Kalkulasi subtotal dari cart
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * ($item['quantity'] ?? $item['qty']);
        }

        $discountPercent = (float) $request->input('discount_percent', 0);
        $manualDiscount = (float) $request->input('discount_amount', 0);
        $percentDiscount = $subtotal * ($discountPercent / 100);
        $totalDiscount = $percentDiscount + $manualDiscount;

        $tax = (float) $request->input('tax', 0);
        $otherFee = (float) $request->input('other_fee', 0);

        $grandTotal = max(0, $subtotal - $totalDiscount + $tax + $otherFee);
        $paidAmount = (float) $request->paid_amount;

        if ($paidAmount < $grandTotal) {
            $kurang = $grandTotal - $paidAmount;
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Uang pembayaran kurang Rp " . number_format($kurang, 0, ',', '.')
                ], 400);
            }
            return redirect()->back()->with('error', 'Uang pembayaran masih kurang!');
        }

        $changeAmount = $paidAmount - $grandTotal;

        try {
            $transaction = DB::transaction(function () use (
                $request,
                $cart,
                $subtotal,
                $totalDiscount,
                $tax,
                $otherFee,
                $grandTotal,
                $paidAmount,
                $changeAmount
            ) {
                // 1. Verifikasi stok setiap produk
                foreach ($cart as $item) {
                    $qty = $item['quantity'] ?? $item['qty'];
                    $product = Product::lockForUpdate()->find($item['id']);
                    if (!$product || $product->stock < $qty) {
                        throw new \Exception("Stok barang '{$item['name']}' tidak mencukupi!");
                    }
                }

                // 2. Buat Nomor Transaksi otomatis sesuai tanggal
                $todayCount = Transaction::whereDate('created_at', today())->count() + 1;
                $invoiceNumber = 'TRX-' . date('Ymd') . '-' . sprintf('%03d', $todayCount);

                $newTransaction = Transaction::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id'        => Auth::id() ?? 1,
                    'customer_name'  => $request->filled('customer_name') ? $request->customer_name : 'Umum',
                    'member_number'  => $request->member_number,
                    'subtotal'       => $subtotal,
                    'discount'       => $totalDiscount,
                    'tax'            => $tax,
                    'other_fee'      => $otherFee,
                    'total_amount'   => $grandTotal,
                    'paid_amount'    => $paidAmount,
                    'change_amount'  => $changeAmount,
                    'payment_method' => $request->payment_method,
                    'status'         => 'selesai',
                    'notes'          => $request->notes,
                ]);

                // 3. Simpan Detail Item dan Kurangi Stok Produk
                foreach ($cart as $item) {
                    $qty = $item['quantity'] ?? $item['qty'];
                    TransactionItem::create([
                        'transaction_id' => $newTransaction->id,
                        'product_id'     => $item['id'],
                        'product_code'   => $item['code'] ?? ($item['sku'] ?? null),
                        'product_name'   => $item['name'],
                        'price'          => $item['price'],
                        'quantity'       => $qty,
                        'subtotal'       => $item['price'] * $qty,
                    ]);

                    Product::where('id', $item['id'])->decrement('stock', $qty);
                }

                return $newTransaction;
            });

            // Kosongkan keranjang session
            session()->forget('cart');

            if ($request->wantsJson()) {
                $receiptUrl = $request->getSchemeAndHttpHost() . '/cashier/receipt/' . $transaction->id;
                return response()->json([
                    'success'      => true,
                    'message'      => "Pembayaran berhasil!\n\nTotal: Rp " . number_format($grandTotal, 0, ',', '.') . "\nBayar: Rp " . number_format($paidAmount, 0, ',', '.') . "\nKembali: Rp " . number_format($changeAmount, 0, ',', '.'),
                    'transaction'  => $transaction->load('items'),
                    'redirect_url' => $receiptUrl,
                    'receipt_path' => '/cashier/receipt/' . $transaction->id,
                ]);
            }

            return redirect()->route('cashier.receipt', $transaction->id)
                ->with('success', "Transaksi berhasil! [{$transaction->invoice_number}]");

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Struk Pembelian (Thermal Receipt) siap cetak.
     */
    public function receipt($id)
    {
        $transaction = Transaction::with(['items', 'user'])->findOrFail($id);

        return view('cashier.receipt', compact('transaction'));
    }

    /**
     * Riwayat Transaksi Kasir.
     */
    public function history()
    {
        $transactions = Transaction::with(['user', 'items'])->latest()->paginate(15);

        return view('cashier.history', compact('transactions'));
    }

    /**
     * Kirim Struk Transaksi ke Email Pelanggan (Integrasi Acara 32 - Laravel Email).
     */
    public function sendReceiptEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email pelanggan wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
        ]);

        $transaction = Transaction::with(['items', 'user'])->findOrFail($id);

        try {
            \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\TransactionReceiptMail($transaction));

            return redirect()->back()->with('success', "Struk transaksi #{$transaction->invoice_number} berhasil dikirimkan ke {$request->email}!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', "Gagal mengirimkan email: " . $e->getMessage());
        }
    }
}
