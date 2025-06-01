<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear categoría de prueba
        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Category for testing',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function can_get_products_list()
    {
        // Crear productos de prueba
        Product::factory(3)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'products',
                    'pagination'
                ],
                'response_time'
            ]);
    }

    /** @test */
    public function can_create_product_with_valid_token()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 19990.50,
            'stock' => 10,
            'category_id' => $this->category->id,
            'weight' => 100.0,
            'color' => 'Blue',
            'material' => 'Cotton',
            'status' => 'active'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer tejelanas_admin_token_2025'
        ])->postJson('/api/v1/products', $productData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'response_time'
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 19990.50
        ]);
    }

    /** @test */
    public function cannot_create_product_without_token()
    {
        $productData = [
            'name' => 'Test Product',
            'price' => 19990.50,
            'stock' => 10,
            'category_id' => $this->category->id
        ];

        $response = $this->postJson('/api/v1/products', $productData);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Sin autorización'
            ]);
    }

    /** @test */
    public function can_update_product()
    {
        $product = Product::create([
            'name' => 'Original Product',
            'description' => 'Original Description',
            'price' => 15000.00,
            'stock' => 5,
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);

        $updateData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 18000.00,
            'stock' => 8,
            'category_id' => $this->category->id,
            'status' => 'active'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer tejelanas_admin_token_2025'
        ])->putJson("/api/v1/products/{$product->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 18000.00
        ]);
    }

    /** @test */
    public function can_toggle_product_status()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 15000.00,
            'stock' => 5,
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer tejelanas_admin_token_2025'
        ])->patchJson("/api/v1/products/{$product->id}/toggle-status");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'previous_status' => 'active',
                    'current_status' => 'inactive'
                ]
            ]);
    }

    /** @test */
    public function can_delete_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 15000.00,
            'stock' => 5,
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer tejelanas_admin_token_2025'
        ])->deleteJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);
    }
}