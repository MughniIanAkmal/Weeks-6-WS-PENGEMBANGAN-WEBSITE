<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * ACARA 13: Resource Controller
     * Menampilkan daftar semua produk (index) - Bisa dilihat semua pengguna.
     */
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form tambah produk (create) - HANYA ADMIN (Otorisasi Gate/Policy Acara 24).
     */
    public function create()
    {
        if (Gate::denies('manage-products') || Gate::denies('create', Product::class)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menambah data (Hanya Admin).');
        }

        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database (store) - HANYA ADMIN (Otorisasi Policy Acara 24).
     */
    public function store(Request $request)
    {
        if (Gate::denies('create', Product::class)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menambah data (Hanya Admin).');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|unique:products,sku|max:50',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        Product::create($request->only(['category_id', 'name', 'sku', 'price', 'stock']));

        return redirect()->route('home')
            ->with('success', 'Produk baru berhasil ditambahkan melalui ProductController::store() (Divalidasi oleh ProductPolicy)!');
    }

    /**
     * Menampilkan detail produk tertentu (show) - Bisa dilihat semua pengguna.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk (edit) - HANYA ADMIN (Otorisasi Gate/Policy Acara 24).
     */
    public function edit(Product $product)
    {
        if (Gate::denies('update', $product)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk mengedit data (Hanya Admin).');
        }

        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk di database (update) - HANYA ADMIN (Otorisasi Policy Acara 24).
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (Gate::denies('update', $product)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk memperbarui data (Hanya Admin).');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|max:50|unique:products,sku,' . $id,
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $product->update($request->only(['category_id', 'name', 'sku', 'price', 'stock']));

        return redirect()->route('home')
            ->with('success', "Produk [ID: {$id}] berhasil diperbarui melalui ProductController::update() (Disahkan oleh ProductPolicy)!");
    }

    /**
     * Menghapus produk dari database (destroy) - HANYA ADMIN (Otorisasi Policy Acara 24).
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (Gate::denies('delete', $product)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menghapus data (Hanya Admin).');
        }

        $product->delete();

        return redirect()->route('home')
            ->with('success', "Produk [ID: {$id}] berhasil dihapus melalui ProductController::destroy()!");
    }
}
