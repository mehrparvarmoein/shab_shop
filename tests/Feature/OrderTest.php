<?php

namespace Tests\Feature\API;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_order()
    {
        $user = User::factory()->create();

        $product1 = Product::factory()->create([
            'user_id'        => $user->id,
            'price'          => 10,
            'shipping_price' => 5,
        ]);

        $product2 = Product::factory()->create([
            'user_id'        => $user->id,
            'price'          => 15,
            'shipping_price' => 7,
        ]);

        $data = [
            [
                'product_id' => $product1->id,
                'quantity'   => 2,
            ],
            [
                'product_id' => $product2->id,
                'quantity'   => 3,
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'products' => $data,
        ]);

        $totalPrice = 10 * 2 + 15 * 3;

        $response->assertOk()
            ->assertJson([
                'message'     => 'Order saved successfully',
                'total_price' => $totalPrice,
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id'     => auth()->id(),
            'total_price' => $totalPrice,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id'     => $product1->id,
            'quantity'       => 2,
            'price'          => 10,
            'shipping_price' => 5,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id'     => $product2->id,
            'quantity'       => 3,
            'price'          => 15,
            'shipping_price' => 7,
        ]);
    }
}
