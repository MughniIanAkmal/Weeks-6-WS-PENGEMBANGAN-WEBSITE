@extends('layouts.app')

@section('title', 'Contoh Query Builder - Acara 17')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Contoh Kode Acara 17 (Query Builder)</h4>
    <a href="{{ route('acara17.products.index') }}" class="btn btn-secondary btn-sm">Kembali ke CRUD</a>
</div>

<div class="row">
    <!-- 1 & 2: Insert & Select -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">1 &amp; 2. Insert &amp; Select Data</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// Insert Data (Halaman 2)
DB::table('users')->insert([
    'name' => 'John Doe',
    'email' => 'johndoe@example.com',
    'password' => bcrypt('password123')
]);

// Ambil Baris Pertama (Halaman 3)
$first = DB::table('products')->first();</code></pre>
                <div class="small">
                    <strong>Contoh Data:</strong> {{ $firstProduct->name ?? '-' }} (ID: {{ $firstProduct->id ?? '-' }})
                </div>
            </div>
        </div>
    </div>

    <!-- 3 & 4: Update & Delete -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">3 &amp; 4. Update &amp; Delete</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// Update Data (Halaman 3)
DB::table('products')->where('id', 1)->update(['price' => 20000]);

// Increment / Decrement (Halaman 4)
DB::table('products')->where('id', 1)->increment('stock', 10);
DB::table('products')->where('id', 1)->decrement('stock', 5);

// Hapus Data (Halaman 4)
DB::table('products')->where('id', 1)->delete();</code></pre>
            </div>
        </div>
    </div>

    <!-- 5 & 6: Pluck & Agregat -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">5 &amp; 6. Pluck &amp; Agregat</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// Pluck Nama Kategori (Halaman 4)
$names = DB::table('categories')->pluck('name');

// Agregat (Halaman 4-5)
$total = DB::table('products')->count();
$avg = DB::table('products')->avg('price');</code></pre>
                <div class="small">
                    <strong>Hasil Pluck:</strong> {{ $pluckedNames->implode(', ') }}<br>
                    <strong>Total Produk:</strong> {{ $aggregates['count'] }} | <strong>Rata-rata Harga:</strong> Rp {{ number_format($aggregates['avg_price'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- 7 & 8: Join & OrderBy -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">7 &amp; 8. Join &amp; Order By</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// Inner Join (Halaman 5)
$products = DB::table('products')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->select('products.name', 'categories.name as category_name')
    ->get();

// Order By & Limit (Halaman 5)
$list = DB::table('products')->orderBy('name', 'asc')->limit(5)->get();</code></pre>
                <div class="small">
                    <strong>Contoh Join:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($innerJoinData->take(3) as $row)
                            <li>{{ $row->product_name }} &mdash; <em>{{ $row->category_name }}</em></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
