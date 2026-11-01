<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DiningTable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RestaurantSetting;
use App\Services\OrderPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['diningTable', 'user', 'items'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'statuses' => Order::statusLabels(),
            'types' => Order::typeLabels(),
        ]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'tables' => DiningTable::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::with('category')->where('is_active', true)->where('is_available', true)->orderBy('name')->get(),
            'types' => Order::typeLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:dine_in,takeaway,delivery'],
            'dining_table_id' => ['nullable', 'required_if:type,dine_in', 'exists:dining_tables,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'delivery_address' => ['nullable', 'required_if:type,delivery', 'string', 'max:500'],
            'covers' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $order = Order::create([
                'number' => $this->nextNumber(),
                'type' => $data['type'],
                'status' => Order::STATUS_OUVERTE,
                'dining_table_id' => $data['type'] === Order::TYPE_DINE_IN ? ($data['dining_table_id'] ?? null) : null,
                'user_id' => auth()->id(),
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
                'covers' => $data['covers'] ?? 1,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $row) {
                $this->addItem($order, (int) $row['product_id'], (int) $row['quantity'], $row['notes'] ?? null);
            }

            $settings = RestaurantSetting::current();
            $order->recalculateTotals((float) $settings->tax_rate);

            if ($order->dining_table_id) {
                DiningTable::whereKey($order->dining_table_id)->update(['status' => DiningTable::STATUS_OCCUPEE]);
            }

            ActivityLog::record('order.create', $order, 'Création commande '.$order->number);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Commande créée.');
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'payments', 'diningTable', 'user', 'cashier']);

        return view('orders.show', [
            'order' => $order,
            'products' => Product::where('is_active', true)->where('is_available', true)->orderBy('name')->get(),
            'methods' => \App\Models\Payment::METHODS,
            'settings' => RestaurantSetting::current(),
        ]);
    }

    public function addItems(Request $request, Order $order): RedirectResponse
    {
        if (in_array($order->status, [Order::STATUS_PAYEE, Order::STATUS_ANNULEE], true)) {
            return back()->withErrors(['order' => 'Commande verrouillée.']);
        }

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->addItem($order, (int) $data['product_id'], (int) $data['quantity'], $data['notes'] ?? null);
        $order->recalculateTotals((float) RestaurantSetting::current()->tax_rate);

        if ($order->status === Order::STATUS_OUVERTE) {
            $order->update(['status' => Order::STATUS_EN_PREPARATION]);
        }

        return back()->with('success', 'Article ajouté.');
    }

    public function sendToKitchen(Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_OUVERTE) {
            $order->update(['status' => Order::STATUS_EN_PREPARATION]);
            $order->items()->where('kitchen_status', 'pending')->update(['kitchen_status' => 'preparing']);
        }

        return back()->with('success', 'Envoyé en cuisine.');
    }

    public function cancel(Request $request, Order $order, OrderPaymentService $payments): RedirectResponse
    {
        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        try {
            $payments->cancel($order, $data['cancel_reason']);
        } catch (\Throwable $e) {
            return back()->withErrors(['order' => $e->getMessage()]);
        }

        return redirect()->route('orders.index')->with('success', 'Commande annulée (avec motif).');
    }

    private function addItem(Order $order, int $productId, int $qty, ?string $notes): OrderItem
    {
        $product = Product::findOrFail($productId);
        $unitCost = (float) $product->cost_price;
        if ($product->recipe) {
            $unitCost = $product->recipe->computeCost();
        }

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'unit_cost' => $unitCost,
            'quantity' => $qty,
            'line_total' => round((float) $product->price * $qty, 2),
            'line_cost' => round($unitCost * $qty, 2),
            'kitchen_status' => 'pending',
            'notes' => $notes,
        ]);
    }

    private function nextNumber(): string
    {
        $prefix = 'CMD-'.now()->format('Ymd').'-';
        $last = Order::where('number', 'like', $prefix.'%')->orderByDesc('id')->value('number');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
