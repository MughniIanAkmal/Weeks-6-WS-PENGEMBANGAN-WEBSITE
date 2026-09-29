<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);
    }

    public function test_product_resource_routes_work(): void
    {
        $cat = Category::first();
        $admin = \App\Models\User::create([
            'name'     => 'Admin User',
            'email'    => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        // 1. Create product via store()
        $resStore = $this->actingAs($admin)->post(route('product.store'), [
            'category_id' => $cat->id,
            'name'        => 'Biskuit Roma',
            'sku'         => 'BSK-001',
            'price'       => 10000,
            'stock'       => 20,
        ]);
        $resStore->assertRedirect(route('home'));
        $this->assertDatabaseHas('products', ['sku' => 'BSK-001']);

        $product = Product::where('sku', 'BSK-001')->first();

        // 2. index()
        $resIndex = $this->get(route('product.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Biskuit Roma');

        // 3. show()
        $resShow = $this->get(route('product.show', $product->id));
        $resShow->assertStatus(200);
        $resShow->assertSee('Biskuit Roma');

        // 4. edit()
        $resEdit = $this->actingAs($admin)->get(route('product.edit', $product->id));
        $resEdit->assertStatus(200);

        // 5. update()
        $resUpdate = $this->actingAs($admin)->put(route('product.update', $product->id), [
            'category_id' => $cat->id,
            'name'        => 'Biskuit Roma Kelapa',
            'sku'         => 'BSK-001',
            'price'       => 12000,
            'stock'       => 25,
        ]);
        $resUpdate->assertRedirect(route('home'));
        $this->assertDatabaseHas('products', ['name' => 'Biskuit Roma Kelapa', 'price' => 12000]);

        // 6. destroy()
        $resDestroy = $this->actingAs($admin)->delete(route('product.destroy', $product->id));
        $resDestroy->assertRedirect(route('home'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_api_product_controller_returns_json(): void
    {
        $cat = Category::first();
        Product::create([
            'category_id' => $cat->id,
            'name'        => 'Teh Botol Sosro',
            'sku'         => 'TEH-001',
            'price'       => 4000,
            'stock'       => 50,
        ]);

        $response = $this->get('/api/product');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Teh Botol Sosro']);
    }
}
