<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $dayRevenue = (float) Order::query()
            ->where('status', Order::STATUS_PAYEE)
            ->whereDate('validated_at', $today)
            ->sum('total');

        $monthSales = (float) Order::query()
            ->where('status', Order::STATUS_PAYEE)
            ->whereDate('validated_at', '>=', $monthStart)
            ->sum('total');

        $monthCost = (float) Order::query()
            ->where('status', Order::STATUS_PAYEE)
            ->whereDate('validated_at', '>=', $monthStart)
            ->sum('cost_total');

        $ordersToday = Order::query()->whereDate('created_at', $today)->count();

        $statusCounts = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->whereDate('created_at', $today)
            ->groupBy('status')
            ->pluck('total', 'status');

        $lowStockIngredients = Ingredient::query()
            ->whereColumn('stock_qty', '<=', 'stock_alert')
            ->where('is_active', true)
            ->orderBy('stock_qty')
            ->limit(8)
            ->get();

        $lowStockProducts = Product::query()
            ->where('track_stock', true)
            ->whereColumn('stock_qty', '<=', 'stock_alert')
            ->where('is_active', true)
            ->limit(8)
            ->get();

        $chartDays = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i)->toDateString();
            $total = (float) Order::query()
                ->where('status', Order::STATUS_PAYEE)
                ->whereDate('validated_at', $date)
                ->sum('total');

            return ['label' => now()->subDays($i)->format('d/m'), 'total' => $total];
        });

        return view('dashboard.index', compact(
            'dayRevenue',
            'monthSales',
            'monthCost',
            'ordersToday',
            'statusCounts',
            'lowStockIngredients',
            'lowStockProducts',
            'chartDays'
        ));
    }
}
