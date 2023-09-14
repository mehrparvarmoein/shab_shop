<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function newOrder($data)
    {
        $orderItems = $this->createOrderItems($data);

        return DB::transaction(function () use ($orderItems) {
            $order = Order::create([
                'user_id'     => auth()->id(),
                'total_price' => $this->calculatePrice($orderItems),
            ]);
            $order->items()->saveMany($orderItems);

            return $order;
        });

        ///admin email will be sent by OrderObserver with queueable notification///

    }

    private function createOrderItems($data)
    {
        $orderItems = [];

        foreach ($data['products'] as $item) {
            $product = Product::where('id',$item['product_id'])
                ->when($data['shipping'], function ($query) {
                    $query->withShipping();
                })->first();

            if ($product) {
                $orderItems[] = new OrderItem([
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'price'          => $product->price,
                    'shipping_price' => $product->shipping_price,
                ]);
            }
        }

        return $orderItems;
    }

    private function calculatePrice($orderItems)
    {
        $totalPrice = 0;

        foreach ($orderItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        return $totalPrice;
    }
}
