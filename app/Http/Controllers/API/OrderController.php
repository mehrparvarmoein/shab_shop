<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function store(StoreOrderRequest $request)
    {
        $order =  $this->orderService->newOrder($request);

        return response()->json([
            'message'     => 'Order saved successfully',
            'total_price' => $order->total_price,
        ]);
    }
}
