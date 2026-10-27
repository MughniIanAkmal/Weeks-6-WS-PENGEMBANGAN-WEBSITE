<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk (Katalog & Manajemen Data Produk).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $query = Product::with('category');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest('id')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    /**
     * Menampilkan form tambah produk baru (Hanya Admin).
     */
    public function create()
    {
        if (Gate::denies('manage-products') || Gate::denies('create', Product::class)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! Hanya Administrator yang memiliki hak akses menambah produk.');
        }

        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database beserta upload & resize gambar (Intervention Image v3).
     */
    public function store(Request $request)
    {
        if (Gate::denies('create', Product::class)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk menambah produk.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|unique:products,sku|max:50',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'name.required'        => 'Nama produk wajib diisi.',
            'name.min'             => 'Nama produk minimal 3 karakter.',
            'sku.required'         => 'Kode SKU produk wajib diisi.',
            'sku.unique'           => 'Kode SKU sudah digunakan produk lain.',
            'price.required'       => 'Harga produk wajib diisi.',
            'stock.required'       => 'Stok produk wajib diisi.',
            'image.image'          => 'File yang diupload harus berupa gambar.',
            'image.mimes'          => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'image.max'            => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $data = $request->only(['category_id', 'name', 'sku', 'price', 'stock']);

        // Upload Gambar & Buat Thumbnail 300x200 (Intervention Image v3)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Pastikan folder storage publik tersedia
            Storage::disk('public')->makeDirectory('products');
            Storage::disk('public')->makeDirectory('products/thumbnails');

            // 1. Simpan foto original ke storage/app/public/products
            $file->storeAs('products', $filename, 'public');

            // 2. Buat thumbnail ukuran 300x200 menggunakan Intervention Image v3
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                $image->cover(300, 200);
                $thumbnailPath = storage_path('app/public/products/thumbnails/' . $filename);
                $image->save($thumbnailPath);
            } catch (\Throwable $e) {
                // Fallback jika terjadi kendala pada driver resize, file original tetap tersimpan
            }

            $data['image'] = $filename;
        }

        Product::create($data);

        return redirect()->route('home')
            ->with('success', 'Produk baru berhasil ditambahkan beserta foto produk!');
    }

    /**
     * Menampilkan detail produk tertentu.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk (Hanya Admin).
     */
    public function edit(Product $product)
    {
        if (Gate::denies('update', $product)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengedit produk ini.');
        }

        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk di database beserta penanganan ganti foto produk & thumbnail.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if (Gate::denies('update', $product)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk memperbarui produk ini.');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|max:50|unique:products,sku,' . $id,
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'name.required'        => 'Nama produk wajib diisi.',
            'sku.required'         => 'Kode SKU produk wajib diisi.',
            'sku.unique'           => 'Kode SKU sudah digunakan produk lain.',
            'price.required'       => 'Harga produk wajib diisi.',
            'stock.required'       => 'Stok produk wajib diisi.',
            'image.image'          => 'File yang diupload harus berupa gambar.',
            'image.mimes'          => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'image.max'            => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $data = $request->only(['category_id', 'name', 'sku', 'price', 'stock']);

        // Jika ada unggahan gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dan thumbnail lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete('products/' . $product->image);
                Storage::disk('public')->delete('products/thumbnails/' . $product->image);
            }

            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Pastikan direktori tersedia
            Storage::disk('public')->makeDirectory('products');
            Storage::disk('public')->makeDirectory('products/thumbnails');

            // 1. Simpan foto original baru
            $file->storeAs('products', $filename, 'public');

            // 2. Buat thumbnail 300x200 dengan Intervention Image v3
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getRealPath());
                $image->cover(300, 200);
                $thumbnailPath = storage_path('app/public/products/thumbnails/' . $filename);
                $image->save($thumbnailPath);
            } catch (\Throwable $e) {
                // Fallback
            }

            $data['image'] = $filename;
        }

        $product->update($data);

        return redirect()->route('home')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Menghapus produk dari database serta membersihkan file gambar fisik dari storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (Gate::denies('delete', $product)) {
            return redirect()->route('home')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk menghapus produk ini.');
        }

        // Hapus file gambar dan thumbnail dari storage
        if ($product->image) {
            Storage::disk('public')->delete('products/' . $product->image);
            Storage::disk('public')->delete('products/thumbnails/' . $product->image);
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('home')
            ->with('success', "Produk '{$productName}' berhasil dihapus dari sistem!");
    }
}
