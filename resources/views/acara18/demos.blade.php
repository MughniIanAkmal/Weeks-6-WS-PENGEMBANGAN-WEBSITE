@extends('layouts.app')

@section('title', 'Contoh Eloquent ORM - Acara 18')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-bold">Contoh Kode Acara 18 (Eloquent ORM)</h4>
    <a href="{{ route('acara18.products.index') }}" class="btn btn-secondary btn-sm">Kembali ke CRUD</a>
</div>

<div class="row">
    <!-- 1. Menambahkan Data (Create) -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">1. Menambahkan Data ke Database (Create)</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// a) Menggunakan create() (PDF Hal. 9)
User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password')
]);

// b) Menggunakan save() (PDF Hal. 10)
$user = new User;
$user->name = 'Jane Doe';
$user->email = 'jane@example.com';
$user->password = bcrypt('password');
$user->save();</code></pre>
            </div>
        </div>
    </div>

    <!-- 2. Mengambil Data (Retrieve) -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">2. Mengambil Data dari Database (Retrieve)</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// a) Mengambil Semua Data (PDF Hal. 10)
$users = User::all();

// b) Mengambil Berdasarkan ID (PDF Hal. 10)
$user = User::find(1);

// c) Menggunakan Query Builder (PDF Hal. 10)
$users = User::where('email', '...')->get();

// d) Menggunakan firstOrFail() (PDF Hal. 10)
$user = User::where('email', '...')->firstOrFail();</code></pre>
                <div class="small">
                    <strong>Contoh Data find(1):</strong> {{ $firstProduct->name ?? '-' }} (SKU: {{ $firstProduct->sku ?? '-' }})
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Memperbarui Data (Update) -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">3. Memperbarui Data (Update)</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// a) Menggunakan update() (PDF Hal. 10)
User::where('email', '...')->update(['name' => 'John Updated']);

// b) Menggunakan save() (PDF Hal. 10)
$user = User::find(1);
$user->name = 'John Updated';
$user->save();</code></pre>
            </div>
        </div>
    </div>

    <!-- 4. Menghapus Data (Delete) -->
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header fw-bold bg-white">4. Menghapus Data (Delete)</div>
            <div class="card-body">
                <pre class="bg-light p-2 rounded border small"><code>// a) Menggunakan delete() (PDF Hal. 11)
$user = User::find(1);
$user->delete();

// b) Menggunakan destroy() (PDF Hal. 11)
User::destroy(1);</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
