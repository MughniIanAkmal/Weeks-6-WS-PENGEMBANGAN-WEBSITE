<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Acara17Controller extends Controller
{
    /**
     * Display a listing of products using Query Builder.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.stock',
                'products.created_at',
                'categories.name as category_name'
            );

        if (!empty($search)) {
            $query->where('products.name', 'like', "%{$search}%")
                  ->orWhere('products.sku', 'like', "%{$search}%");
        }

        if (!empty($categoryId)) {
            $query->where('products.category_id', $categoryId);
        }

        $products = $query->orderBy('products.id', 'desc')->paginate(10)->withQueryString();

        // Query Builder Aggregates for Dashboard Statistics
        $stats = [
            'total_products' => DB::table('products')->count(),
            'total_stock'    => DB::table('products')->sum('stock'),
            'avg_price'      => DB::table('products')->avg('price'),
            'max_price'      => DB::table('products')->max('price'),
            'min_price'      => DB::table('products')->min('price'),
        ];

        $categories = DB::table('categories')->orderBy('name', 'asc')->get();

        return view('acara17.index', compact('products', 'categories', 'stats', 'search', 'categoryId'));
    }

    /**
     * Show form to create product with Query Builder.
     */
    public function create()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menambah produk (Hanya Admin).');
        }

        $categories = DB::table('categories')->orderBy('name', 'asc')->get();
        return view('acara17.create', compact('categories'));
    }

    /**
     * Store newly created product using DB::table()->insertGetId().
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menambah produk (Hanya Admin).');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|unique:products,sku|max:50',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        // Prosedur Kerja Acara 17: Insert dan Mendapatkan ID yang Dihasilkan
        $newId = DB::table('products')->insertGetId([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'sku'         => $request->sku,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('acara17.products.index')
            ->with('success', "Produk baru berhasil disimpan via Query Builder insertGetId() [ID: {$newId}]!");
    }

    /**
     * Show form for editing product using DB::table()->where()->first().
     */
    public function edit($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk mengedit produk (Hanya Admin).');
        }

        // Prosedur Kerja Acara 17: Mengambil Data Berdasarkan Kondisi (first())
        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Produk tidak ditemukan!');
        }

        $categories = DB::table('categories')->orderBy('name', 'asc')->get();

        return view('acara17.edit', compact('product', 'categories'));
    }

    /**
     * Update product using DB::table()->where()->update().
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk mengupdate produk (Hanya Admin).');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|max:50|unique:products,sku,' . $id,
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        // Prosedur Kerja Acara 17: Memperbarui Data (update)
        $affected = DB::table('products')
            ->where('id', $id)
            ->update([
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'sku'         => $request->sku,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'updated_at'  => now(),
            ]);

        return redirect()->route('acara17.products.index')
            ->with('success', "Produk [ID: {$id}] berhasil diperbarui via Query Builder update()! ({$affected} baris diperbarui)");
    }

    /**
     * Adjust stock using increment / decrement.
     */
    public function adjustStock(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk mengubah stok (Hanya Admin).');
        }

        $action = $request->input('action', 'increment');
        $amount = (int) $request->input('amount', 1);

        if ($action === 'decrement') {
            // Prosedur Kerja Acara 17: Decrement
            DB::table('products')->where('id', $id)->decrement('stock', $amount);
            $msg = "Stok berkurang {$amount} via DB::table()->decrement()";
        } else {
            // Prosedur Kerja Acara 17: Increment
            DB::table('products')->where('id', $id)->increment('stock', $amount);
            $msg = "Stok bertambah {$amount} via DB::table()->increment()";
        }

        return redirect()->back()->with('info', $msg);
    }

    /**
     * Remove product using DB::table()->where()->delete().
     */
    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara17.products.index')
                ->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menghapus produk (Hanya Admin).');
        }

        // Prosedur Kerja Acara 17: Menghapus Data (delete)
        DB::table('products')->where('id', $id)->delete();

        return redirect()->route('acara17.products.index')
            ->with('success', "Produk [ID: {$id}] berhasil dihapus via Query Builder delete()!");
    }

    /**
     * Showcase & Interactive Demo for all 10 Query Builder procedures from Acara 17 BKPM.
     */
    public function demos()
    {
        // 1) Ambil Data Semua (get)
        $allUsers = DB::table('users')->get();

        // 2) Ambil Data Berdasarkan Kondisi (where + first)
        $firstProduct = DB::table('products')->where('id', '>', 0)->first();

        // 3) Select Kolom Tertentu
        $selectedCols = DB::table('products')->select('id', 'name', 'price')->limit(3)->get();

        // 4) Where dengan Operator (misal: stock >= 10 atau price >= 50000)
        $highStock = DB::table('products')->where('stock', '>=', 20)->limit(3)->get();

        // 5) Pluck Kolom
        $pluckedNames = DB::table('categories')->pluck('name');
        $pluckedMap = DB::table('categories')->pluck('name', 'id');

        // 6) Agregat
        $aggregates = [
            'count' => DB::table('products')->count(),
            'sum_stock' => DB::table('products')->sum('stock'),
            'avg_price' => DB::table('products')->avg('price'),
            'max_price' => DB::table('products')->max('price'),
            'min_price' => DB::table('products')->min('price'),
        ];

        // 7) Join (Inner Join & Left Join)
        $innerJoinData = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.name as product_name', 'categories.name as category_name', 'products.price')
            ->limit(4)
            ->get();

        // 8) Order By, Limit, Offset
        $orderedData = DB::table('products')
            ->orderBy('name', 'asc')
            ->offset(0)
            ->limit(4)
            ->get();

        // 9) Subquery (Query di dalam Query)
        $categoriesWithCount = DB::table('categories')
            ->select('name')
            ->selectSub(function ($query) {
                $query->from('products')
                      ->selectRaw('count(*)')
                      ->whereColumn('products.category_id', 'categories.id');
            }, 'order_count')
            ->get();

        // 10) Query Raw (Raw SQL)
        $rawGroup = DB::table('products')
            ->selectRaw('category_id, COUNT(*) as total_users, status')
            ->groupBy('status')
            ->get();

        $rawWhere = DB::table('products')
            ->whereRaw('age > ? AND status = ?', [18, 'active'])
            ->limit(3)
            ->get();

        return view('acara17.demos', compact(
            'allUsers',
            'firstProduct',
            'selectedCols',
            'highStock',
            'pluckedNames',
            'pluckedMap',
            'aggregates',
            'innerJoinData',
            'orderedData',
            'categoriesWithCount',
            'rawGroup',
            'rawWhere'
        ));
    }
}
