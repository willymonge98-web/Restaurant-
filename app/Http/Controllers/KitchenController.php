<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['items.product', 'diningTable'])
            ->whereIn('status', [
                Order::STATUS_OUVERTE,
                Order::STATUS_EN_PREPARATION,
                Order::STATUS_PRETE,
            ])
            ->latest()
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    public function updateItem(Request $request, OrderItem $item): RedirectResponse
    {
        $data = $request->validate([
            'kitchen_status' => ['required', 'in:pending,preparing,ready,served,cancelled'],
        ]);

        $item->update($data);
        $order = $item->order()->with('items')->first();

        $active = $order->items->whereNotIn('kitchen_status', ['cancelled']);
        if ($active->isNotEmpty() && $active->every(fn ($i) => $i->kitchen_status === 'ready')) {
            $order->update(['status' => Order::STATUS_PRETE]);
        } elseif ($active->contains(fn ($i) => in_array($i->kitchen_status, ['preparing', 'ready'], true))) {
            if (! in_array($order->status, [Order::STATUS_PAYEE, Order::STATUS_ANNULEE, Order::STATUS_SERVIE], true)) {
                $order->update(['status' => Order::STATUS_EN_PREPARATION]);
            }
        }

        return back()->with('success', 'Statut cuisine mis à jour.');
    }

    public function markOrderReady(Order $order): RedirectResponse
    {
        $order->items()->whereNotIn('kitchen_status', ['cancelled'])->update(['kitchen_status' => 'ready']);
        $order->update(['status' => Order::STATUS_PRETE]);

        return back()->with('success', 'Commande marquée prête.');
    }
}
