<?php

namespace Tests\Feature\API;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    

    public function test_user_can_get_all_products()
    {
        $user = User::factory()->create();

        Product::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/products');


        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'price',
                    ],
                ],
            ]);
    }


    public function test_user_cannot_create_product_without_login()
    {
        $data = [
            'title'          => $this->faker->name,
            'price'          => $this->faker->numberBetween(1000,999999999),
            'shipping_price' => $this->faker->numberBetween(1000,99999),
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertUnauthorized();
    }

    public function test_user_can_create_product()
    {
        $user = User::factory()->create();
        $data = [
            'title'          => $this->faker->name,
            'price'          => $this->faker->numberBetween(1000,999999999),
            'shipping_price' => $this->faker->numberBetween(1000,99999),
        ];

        $response = $this->actingAs($user)->postJson('/api/products', $data);

        $response->assertOk()
            ->assertJson([
                'message' => __('product.create'),
            ]);
    }

    public function test_user_can_not_create_product_with_invalid_data()
    {
        $user = User::factory()->create();
        $data = [
            'price'          => 'price',
            'shipping_price' => $this->faker->numberBetween(1000,99999),
        ];

        $response = $this->actingAs($user)->postJson('/api/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title','price']);
    }


    public function test_user_can_create_product_with_upload_images()
    {
        
        Queue::fake(); // Fake the job dispatch
        Storage::fake('public'); // Use a fake disk for testing

        $user = User::factory()->create();
        $image1 = UploadedFile::fake()->image('product1.jpg');
        $image2 = UploadedFile::fake()->image('product2.jpg');

        $data = [
            'title'          => $this->faker->name,
            'price'          => $this->faker->numberBetween(1000,999999999),
            'shipping_price' => $this->faker->numberBetween(1000,99999),
            'images' => [$image1, $image2],
        ];

        $response = $this->actingAs($user)->postJson('/api/products', $data);

        $response->assertOk()
            ->assertJson([
                'message' => __('product.create'),
            ]);


        $product = Product::latest()->first();

        storage::disk('public')->assertExists($product->images->offsetGet(0)->path);
        Storage::disk('public')->assertExists($product->images->offsetGet(1)->path);
    }

    public function test_user_can_get_product_by_id()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/products/' . $product->id);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id'    => $product->id,
                    'title' => $product->title,
                    'price' => $product->price,
                ],
            ]);
    }

    public function test_user_can_update_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title'          => $this->faker->name,
            'price'          => $this->faker->numberBetween(1000,999999999),
            'shipping_price' => $this->faker->numberBetween(1000,99999),
        ];

        $response = $this->actingAs($user)->putJson('/api/products/' . $product->id, $data);

        $response->assertOk()
            ->assertJson([
                'message' => __('product.update'),
            ]);
    }

    public function test_user_cannot_update_product_of_another_user()
    {
        
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        $anotherUser = User::factory()->create();

        $data = [
            'title'          => $this->faker->name,
            'price'          => $this->faker->numberBetween(1000,999999999),
            'shipping_price' => $this->faker->numberBetween(1000,99999),
        ];

        $response = $this->actingAs($anotherUser)->putJson('/api/products/' . $product->id, $data);

        $response->assertForbidden();
    }

    public function test_user_can_delete_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson('/api/products/' . $product->id);

        $response->assertOk()
            ->assertJson([
                'message' => __('product.delete'),
            ]);
    }

    public function test_user_cannot_delete_product_of_another_user()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)->deleteJson('/api/products/' . $product->id);

        $response->assertForbidden();
    }
}
