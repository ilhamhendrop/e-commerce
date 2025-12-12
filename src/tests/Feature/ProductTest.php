<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        return $this->actingAs($user, 'sanctum');
    }

    public function test_user_can_create_product()
    {
        $this->authenticate();

        $data = [
            'name' => 'Laptop',
            'desc' => 'Laptop Gaming',
            'category' => 'electronics',
            'price' => 10000,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ];

        $respone = $this->postJson('/api/admin/product', $data);
        $respone->assertStatus(200);
        $this->assertDatabaseHas('products', ['name' => 'Laptop']);
    }

    public function test_user_can_read_product_list()
    {
        $this->authenticate();

        Product::factory()->count(3)->create();

        $respone = $this->getJson('/api/admin/product');
        $respone->assertStatus(200);
        $respone->assertJsonCount(3, 'data');
    }

    public function test_user_can_update_product()
    {
        $this->authenticate();

        $product = Product::factory()->create();

        $payload = [
            'name' => 'New Laptop',
            'desc' => 'Updated desc',
            'category' => 'electronics',
            'price' => 999999,
        ];

        $response = $this->patchJson("/api/admin/product/{$product->id}/data", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Laptop'
        ]);
    }

    public function test_user_can_update_product_image()
    {
        $this->authenticate();

        $product = Product::factory()->create([
            'image' => 'product/image/old_image.jpg'
        ]);

        $newImage = UploadedFile::fake()->image('new_product.jpg', 500, 500);

        $response = $this->patchJson(
            "/api/admin/product/{$product->id}/image",
            [
                'image' => $newImage
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
            'image' => 'product/image/old_image.jpg'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_user_can_delete_product()
    {
        $this->authenticate();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/product/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}
