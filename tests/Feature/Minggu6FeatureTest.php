<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Minggu6FeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected User $kasir;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Toko',
            'email'    => 'admin_pos@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->user = User::create([
            'name'     => 'User Biasa',
            'email'    => 'user_biasa@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'user',
        ]);

        $this->kasir = User::create([
            'name'     => 'Kasir Toko',
            'email'    => 'kasir_pos@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'kasir',
        ]);

        $category = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Kopi Arabika',
            'sku'         => 'KOP-101',
            'price'       => 25000,
            'stock'       => 40,
        ]);
    }

    /**
     * ACARA 21: Middleware Admin melindungi operasi create & edit produk
     */
    public function test_authorization_protects_product_management(): void
    {
        // 1. Guest yang mencoba menambah produk ditolak dan dialihkan
        $guestRes = $this->get(route('product.create'));
        $guestRes->assertRedirect(route('home'));
        $guestRes->assertSessionHas('error');

        // 2. User biasa (non-admin) ditolak dan dialihkan ke home
        $userRes = $this->actingAs($this->user)->get(route('product.create'));
        $userRes->assertRedirect(route('home'));
        $userRes->assertSessionHas('error');

        // 3. Admin diizinkan masuk ke formulir tambah produk
        $adminRes = $this->actingAs($this->admin)->get(route('product.create'));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Tambah Produk Baru');
    }

    /**
     * ACARA 22: Autentikasi Manual Registrasi Pengguna
     */
    public function test_user_can_register_new_account(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Ahmad Dani',
            'email'                 => 'ahmad@example.com',
            'role'                  => 'kasir',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'ahmad@example.com',
            'role'  => 'kasir',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * ACARA 23: Retrieving The Authenticated User pada Halaman Profil
     */
    public function test_profile_page_displays_authenticated_user_info(): void
    {
        // Guest tidak bisa membuka profile
        $this->get(route('profile'))->assertRedirect(route('login'));

        // Pengguna terautentikasi dapat melihat nama, email, role, dan ID mereka
        $response = $this->actingAs($this->admin)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertSee('Welcome, ' . $this->admin->name);
        $response->assertSee((string) $this->admin->id);
        $response->assertSee('ADMIN');
    }

    /**
     * ACARA 24: Authorization menggunakan ProductPolicy & Direktif @can di Halaman Home
     */
    public function test_authorization_policy_and_blade_can_directives(): void
    {
        // 1. Tampilan untuk Tamu/User biasa: tombol tambah produk dalam kondisi terkunci
        $homeGuest = $this->get(route('home'));
        $homeGuest->assertStatus(200);
        $homeGuest->assertSee('Tambah Produk (Terkunci)');

        // 2. Tampilan untuk Admin: tombol tambah produk aktif dan tombol aksi edit/hapus tampil
        $homeAdmin = $this->actingAs($this->admin)->get(route('home'));
        $homeAdmin->assertStatus(200);
        $homeAdmin->assertSee('+ Tambah Produk');
        $homeAdmin->assertSee(route('product.edit', $this->product->id));
    }
}
