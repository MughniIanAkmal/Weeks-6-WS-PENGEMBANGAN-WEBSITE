<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndHomeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->regularUser = User::create([
            'name'     => 'Pengguna Biasa',
            'email'    => 'user@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'user',
        ]);

        $this->category = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
        ]);
    }

    public function test_home_page_displays_products_and_single_login(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Kopi Susu Gula Aren',
            'sku'         => 'KOP-001',
            'price'       => 15000,
            'stock'       => 30,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Gula Aren');
        $response->assertSee('Daftar Produk Toko');
        $response->assertSee('Login');
    }

    public function test_regular_user_cannot_access_create_or_store_product(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('product.create'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');

        $storeResponse = $this->actingAs($this->regularUser)
            ->post(route('product.store'), [
                'category_id' => $this->category->id,
                'name'        => 'Produk Ilegal',
                'sku'         => 'ILG-001',
                'price'       => 5000,
                'stock'       => 10,
            ]);

        $storeResponse->assertRedirect(route('home'));
        $this->assertDatabaseMissing('products', ['sku' => 'ILG-001']);
    }

    public function test_admin_can_crud_product(): void
    {
        // 1. Create
        $resStore = $this->actingAs($this->admin)
            ->post(route('product.store'), [
                'category_id' => $this->category->id,
                'name'        => 'Susu Segar',
                'sku'         => 'SSU-001',
                'price'       => 12000,
                'stock'       => 50,
            ]);

        $resStore->assertRedirect(route('home'));
        $this->assertDatabaseHas('products', ['sku' => 'SSU-001']);

        $product = Product::where('sku', 'SSU-001')->first();

        // 2. Update
        $resUpdate = $this->actingAs($this->admin)
            ->put(route('product.update', $product->id), [
                'category_id' => $this->category->id,
                'name'        => 'Susu Segar Strawberry',
                'sku'         => 'SSU-001',
                'price'       => 13000,
                'stock'       => 45,
            ]);

        $resUpdate->assertRedirect(route('home'));
        $this->assertDatabaseHas('products', ['name' => 'Susu Segar Strawberry']);

        // 3. Delete
        $resDelete = $this->actingAs($this->admin)
            ->delete(route('product.destroy', $product->id));

        $resDelete->assertRedirect(route('home'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_quick_login_works(): void
    {
        $response = $this->get(route('quickLogin', 'admin'));
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($this->admin);

        $responseUser = $this->get(route('quickLogin', 'user'));
        $responseUser->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($this->regularUser);
    }
}
