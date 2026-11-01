@extends('layouts.app')
@section('title', 'Commande '.$order->number)
@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="erp-card lg:col-span-2 p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-sm text-stone-500">{{ \App\Models\Order::typeLabels()[$order->type] }} · {{ \App\Models\Order::statusLabels()[$order->status] }}</p>
                <p class="mt-1">Table: {{ $order->diningTable?->name ?? '—' }} · Serveur: {{ $order->user?->name }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (!in_array($order->status, ['payee','annulee']))
                    <form method="POST" action="{{ route('orders.kitchen', $order) }}">@csrf
                        <button class="rounded-xl bg-amber-700 px-3 py-2 text-sm text-white">Envoyer cuisine</button>
                    </form>
                    @if(auth()->user()->canAccess('admin','manager','cashier'))
                        <a href="{{ route('pos.checkout', $order) }}" class="rounded-xl bg-lime-800 px-3 py-2 text-sm text-white">Payer</a>
                    @endif
                @endif
            </div>
        </div>

        <table class="mt-5 min-w-full text-sm">
            <thead class="text-left text-stone-500"><tr><th class="py-2">Article</th><th>Qté</th><th>Cuisine</th><th>Total</th></tr></thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr class="border-t">
                    <td class="py-2">{{ $item->product_name }} @if($item->notes)<span class="text-xs text-stone-500">({{ $item->notes }})</span>@endif</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->kitchen_status }}</td>
                    <td>${{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4 border-t pt-4 text-sm space-y-1">
            <div class="flex justify-between"><span>Sous-total</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
            <div class="flex justify-between"><span>Remise</span><span>-${{ number_format($order->discount_amount, 2) }}</span></div>
            <div class="flex justify-between"><span>Taxe</span><span>${{ number_format($order->tax_amount, 2) }}</span></div>
            <div class="flex justify-between font-semibold text-base"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
            <div class="flex justify-between text-stone-500"><span>Payé</span><span>${{ number_format($order->paid_amount, 2) }}</span></div>
            <div class="flex justify-between text-stone-500"><span>Coût matières</span><span>${{ number_format($order->cost_total, 2) }}</span></div>
        </div>
    </div>

    <div class="space-y-6">
        @if (!in_array($order->status, ['payee','annulee']))
        <form method="POST" action="{{ route('orders.items.add', $order) }}" class="erp-card space-y-3 p-5">
            @csrf
            <h2 class="font-display text-lg">Ajouter un article</h2>
            <select name="product_id" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            <input type="number" name="quantity" value="1" min="1" class="w-full rounded-xl border border-stone-300 px-3 py-2">
            <input name="notes" placeholder="Note" class="w-full rounded-xl border border-stone-300 px-3 py-2">
            <button class="rounded-xl bg-stone-900 px-4 py-2 text-white">Ajouter</button>
        </form>

        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="erp-card space-y-3 p-5" onsubmit="return confirm('Annuler cette commande ?')">
            @csrf
            <h2 class="font-display text-lg text-rose-800">Annuler (avec motif)</h2>
            <textarea name="cancel_reason" rows="3" class="w-full rounded-xl border border-stone-300 px-3 py-2" required placeholder="Motif d'annulation…"></textarea>
            <button class="rounded-xl bg-rose-800 px-4 py-2 text-white">Annuler la vente</button>
        </form>
        @endif

        @if ($order->payments->isNotEmpty())
        <div class="erp-card p-5">
            <h2 class="font-display text-lg">Paiements</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($order->payments as $payment)
                    <li class="flex justify-between rounded-lg bg-stone-50 px-3 py-2">
                        <span>{{ $methods[$payment->method] ?? $payment->method }}</span>
                        <span>${{ number_format($payment->amount_usd ?? $payment->amount, 2) }}</span>
                    </li>
                @endforeach
            </ul>
            @if ($order->status === 'payee')
                <a href="{{ route('pos.receipt.pdf', $order) }}" class="mt-3 inline-block text-lime-800">Télécharger ticket PDF</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
