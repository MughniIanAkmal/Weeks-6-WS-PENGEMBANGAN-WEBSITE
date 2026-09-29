<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Halaman Utama: Daftar Produk Toko & Rangkuman Acara 13, 17, dan 18.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        // Query produk
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

        $products = $query->latest('id')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Statistik Query Builder (Acara 17) & Eloquent (Acara 18)
        $stats = [
            'total_products' => DB::table('products')->count(),
            'total_stock'    => DB::table('products')->sum('stock'),
            'avg_price'      => DB::table('products')->avg('price'),
            'total_category' => DB::table('categories')->count(),
        ];

        return view('home', compact('products', 'categories', 'stats', 'search', 'categoryId'));
    }
}
