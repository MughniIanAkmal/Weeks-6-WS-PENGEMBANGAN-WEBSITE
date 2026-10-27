<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierTest extends TestCase
{
    use RefreshDatabase;

    protected User $kasir;
    protected User $userBiasa;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::create([
            'name'     => 'Kasir Siti',
            'email'    => 'siti_kasir@postoko.com',
            'password' => bcrypt('password123'),
            'role'     => 'kasir',
        ]);

        $this->userBiasa = User::create([
            'name'     => 'User Biasa',
            'email'    => 'user_biasa@postoko.com',
            'password' => bcrypt('password123'),
            'role'     => 'user',
        ]);

        $category = Category::create([
            'name' => 'Makanan Ringan',
            'slug' => 'makanan-ringan',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Biskuit Cokelat',
            'sku'         => 'BSK-001',
            'price'       => 8000,
            'stock'       => 20,
        ]);
    }

    /**
     * Role Kasir dan Admin dapat mengakses POS Kasir, sedangkan User biasa ditolak (Middleware cek.role).
     */
    public function test_kasir_role_can_access_cashier_and_user_is_forbidden(): void
    {
        // User biasa ditolak 403 oleh Middleware cek.role:kasir,admin
        $resUser = $this->actingAs($this->userBiasa)->get(route('cashier.index'));
        $resUser->assertStatus(403);

        // Kasir diizinkan
        $resKasir = $this->actingAs($this->kasir)->get(route('cashier.index'));
        $resKasir->assertStatus(200);
        $resKasir->assertSee('Mesin Kasir');
        $resKasir->assertSee('Biskuit Cokelat');
    }

    /**
     * Kasir dapat menambah item ke keranjang dan mengupdate kuantitas.
     */
    public function test_kasir_can_add_item_to_cart_and_update(): void
    {
        // 1. Tambah ke keranjang
        $response = $this->actingAs($this->kasir)->post(route('cashier.cart.add', $this->product->id), [
            'quantity' => 2,
        ]);

        $response->assertSessionHas('cart');
        $cart = session('cart');
        $this->assertEquals(2, $cart[$this->product->id]['quantity']);

        // 2. Tambah kuantitas via update (action = plus)
        $this->actingAs($this->kasir)->post(route('cashier.cart.update', $this->product->id), [
            'action' => 'plus',
        ]);

        $cart = session('cart');
        $this->assertEquals(3, $cart[$this->product->id]['quantity']);
    }

    /**
     * Kasir dapat melakukan Checkout transaksi, memotong stok produk, dan mencetak struk.
     */
    public function test_kasir_can_checkout_and_generate_receipt(): void
    {
        // 1. Masukkan 3 item ke keranjang (3 x 8.000 = 24.000)
        $this->actingAs($this->kasir)->post(route('cashier.cart.add', $this->product->id), [
            'quantity' => 3,
        ]);

        // 2. Checkout dengan uang tunai 30.000 (kembalian 6.000)
        $checkoutResponse = $this->actingAs($this->kasir)->post(route('cashier.checkout'), [
            'customer_name'  => 'Ibu Ani',
            'payment_method' => 'cash',
            'paid_amount'    => 30000,
        ]);

        // 3. Verifikasi transaksi tersimpan di database
        $this->assertDatabaseHas('transactions', [
            'customer_name' => 'Ibu Ani',
            'total_amount'  => 24000,
            'paid_amount'   => 30000,
            'change_amount' => 6000,
        ]);

        $this->assertDatabaseHas('transaction_items', [
            'product_id' => $this->product->id,
            'quantity'   => 3,
            'subtotal'   => 24000,
        ]);

        // 4. Verifikasi stok produk berkurang (20 - 3 = 17)
        $this->assertEquals(17, $this->product->fresh()->stock);

        // 5. Keranjang otomatis dikosongkan
        $this->assertEmpty(session('cart', []));

        // 6. Pengalihan ke halaman struk
        $transaction = Transaction::latest('id')->first();
        $checkoutResponse->assertRedirect(route('cashier.receipt', $transaction->id));

        // 7. Buka halaman struk dan pastikan informasi lengkap
        $receiptResponse = $this->actingAs($this->kasir)->get(route('cashier.receipt', $transaction->id));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($transaction->invoice_number);
        $receiptResponse->assertSee('Biskuit Cokelat');
        $receiptResponse->assertSee('24.000');
        $receiptResponse->assertSee('Ibu Ani');
        $receiptResponse->assertSee('Cetak Struk');
    }
}
