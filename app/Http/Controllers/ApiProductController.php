<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ApiProductController extends Controller
{
    /**
     * ACARA 13: API Controller (Tanpa View, JSON Response)
     * Mengambil semua produk dalam format JSON.
     */
    public function index()
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar semua produk',
            'data'    => Product::with('category')->get(),
        ]);
    }

    /**
     * Mengambil detail satu produk dalam format JSON.
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail produk',
            'data'    => $product,
        ]);
    }

    /**
     * Menyimpan produk baru melalui API.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|unique:products,sku|max:50',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dibuat.',
            'data'    => $product,
        ], 201);
    }

    /**
     * Memperbarui produk melalui API.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name'        => 'sometimes|min:3|max:100',
            'sku'         => 'sometimes|max:50|unique:products,sku,' . $id,
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui.',
            'data'    => $product,
        ]);
    }

    /**
     * Menghapus produk melalui API.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}
