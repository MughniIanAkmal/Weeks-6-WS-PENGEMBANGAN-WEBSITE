<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class Acara18Controller extends Controller
{
    /**
     * Display listing of products using Eloquent ORM.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        // Eloquent ORM with Eager Loading (Relation)
        $query = Product::with('category');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest('id')->paginate(10)->withQueryString();

        // Eloquent aggregates
        $stats = [
            'total_products' => Product::count(),
            'total_stock'    => Product::sum('stock'),
            'avg_price'      => Product::avg('price'),
            'categories'     => Category::withCount('products')->get(),
        ];

        $categories = Category::orderBy('name')->get();

        return view('acara18.index', compact('products', 'categories', 'stats', 'search', 'categoryId'));
    }

    /**
     * Show form to create product with Eloquent ORM.
     */
    public function create()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara18.products.index')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menambah data (Hanya Admin).');
        }

        // Mengambil semua kategori via Eloquent all()
        $categories = Category::all();
        return view('acara18.create', compact('categories'));
    }

    /**
     * Store product using either Model::create() or $model->save().
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara18.products.index')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menambah data (Hanya Admin).');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|unique:products,sku|max:50',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'method'      => 'required|in:create,save',
        ]);

        if ($request->method === 'create') {
            // Prosedur Kerja Acara 18 (1.a): Menggunakan create()
            $product = Product::create([
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'sku'         => $request->sku,
                'price'       => $request->price,
                'stock'       => $request->stock,
            ]);
            $methodUsed = "Product::create()";
        } else {
            // Prosedur Kerja Acara 18 (1.b): Menggunakan save()
            $product = new Product();
            $product->category_id = $request->category_id;
            $product->name        = $request->name;
            $product->sku         = $request->sku;
            $product->price       = $request->price;
            $product->stock       = $request->stock;
            $product->save();
            $methodUsed = "new Product(); \$product->save()";
        }

        return redirect()->route('acara18.products.index')
            ->with('success', "Produk baru berhasil disimpan menggunakan Eloquent {$methodUsed} [ID: {$product->id}]!");
    }

    /**
     * Show form for editing product using Product::findOrFail() or find().
     */
    public function edit($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara18.products.index')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk mengedit data (Hanya Admin).');
        }

        // Prosedur Kerja Acara 18 (2.b): Mengambil Data Berdasarkan ID
        $product = Product::findOrFail($id);
        $categories = Category::all();

        return view('acara18.edit', compact('product', 'categories'));
    }

    /**
     * Update product using either Model::where()->update() or $model->save().
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara18.products.index')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk mengubah data (Hanya Admin).');
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|min:3|max:100',
            'sku'         => 'required|max:50|unique:products,sku,' . $id,
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'method'      => 'required|in:update,save',
        ]);

        if ($request->method === 'update') {
            // Prosedur Kerja Acara 18 (3.a): Menggunakan update()
            Product::where('id', $id)->update([
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'sku'         => $request->sku,
                'price'       => $request->price,
                'stock'       => $request->stock,
            ]);
            $methodUsed = "Product::where('id', {$id})->update([...])";
        } else {
            // Prosedur Kerja Acara 18 (3.b): Menggunakan save()
            $product = Product::findOrFail($id);
            $product->category_id = $request->category_id;
            $product->name        = $request->name;
            $product->sku         = $request->sku;
            $product->price       = $request->price;
            $product->stock       = $request->stock;
            $product->save();
            $methodUsed = "\$product = Product::find({$id}); \$product->save()";
        }

        return redirect()->route('acara18.products.index')
            ->with('success', "Produk [ID: {$id}] berhasil diperbarui menggunakan Eloquent {$methodUsed}!");
    }

    /**
     * Remove product using either $model->delete() or Product::destroy().
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('acara18.products.index')->with('error', 'Akses ditolak! User biasa tidak memiliki izin untuk menghapus data (Hanya Admin).');
        }

        $method = $request->query('method', 'delete');

        if ($method === 'destroy') {
            // Prosedur Kerja Acara 18 (4.b): Menggunakan destroy()
            Product::destroy($id);
            $methodUsed = "Product::destroy({$id})";
        } else {
            // Prosedur Kerja Acara 18 (4.a): Menggunakan delete()
            $product = Product::findOrFail($id);
            $product->delete();
            $methodUsed = "\$product->delete()";
        }

        return redirect()->route('acara18.products.index')
            ->with('success', "Produk [ID: {$id}] berhasil dihapus menggunakan Eloquent {$methodUsed}!");
    }

    /**
     * Showcase & Interactive Demo for all 4 Eloquent ORM procedures from Acara 18 BKPM.
     */
    public function demos()
    {
        // 1) Mengambil Semua Data (all)
        $allCategories = Category::all();

        // 2) Mengambil Data Berdasarkan ID (find)
        $firstProduct = Product::with('category')->find(1);

        // 3) Menggunakan Query Builder via Eloquent (where()->get())
        $sampleWhereProducts = Product::where('price', '>', 50000)->take(3)->get();

        // 4) firstOrFail contoh sukses
        $firstUser = User::where('id', '>', 0)->firstOrFail();

        // 5) Eloquent Relationship Demo (One to Many & BelongsTo)
        $categoryWithProducts = Category::with(['products' => function ($q) {
            $q->take(3);
        }])->first();

        return view('acara18.demos', compact(
            'allCategories',
            'firstProduct',
            'sampleWhereProducts',
            'firstUser',
            'categoryWithProducts'
        ));
    }
}
