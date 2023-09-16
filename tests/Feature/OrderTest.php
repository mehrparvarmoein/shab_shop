<?php

namespace Tests\Feature\API;

use App\Models\Order;
use App\Models\OrderItem;
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

        $totalPrice = $product1->price * 2 + $product2->price * 3;

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'products' => $data,
        ]);

        $response->assertOk()
            ->assertJson([
                'message'     => 'Order saved successfully',
                'total_price' => $totalPrice,
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id'     => $user->id,
            'total_price' => $totalPrice,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id'     => $product1->id,
            'quantity'       => 2,
            'price'          => $product1->price,
            'shipping_price' => $product1->shipping_price,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id'     => $product2->id,
            'quantity'       => 3,
            'price'          => $product2->price,
            'shipping_price' => $product2->shipping_price,
        ]);
    }
}
