<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['items', 'diningTable'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(30);

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load(['items.product', 'payments', 'diningTable']));
    }

    public function products(): JsonResponse
    {
        return response()->json(
            Product::with('category')
                ->where('is_active', true)
                ->where('is_available', true)
                ->orderBy('name')
                ->get()
        );
    }

    public function kitchen(): JsonResponse
    {
        $orders = Order::with(['items', 'diningTable'])
            ->whereIn('status', [Order::STATUS_OUVERTE, Order::STATUS_EN_PREPARATION, Order::STATUS_PRETE])
            ->latest()
            ->get();

        return response()->json($orders);
    }
}
