@extends('layouts.app')
@section('title', 'Encaissement '.$order->number)
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="erp-card p-5">
        <h2 class="font-display text-xl">Récapitulatif</h2>
        <ul class="mt-4 space-y-2 text-sm">
            @foreach ($order->items as $item)
                <li class="flex justify-between"><span>{{ $item->quantity }}× {{ $item->product_name }}</span><span>${{ number_format($item->line_total, 2) }}</span></li>
            @endforeach
        </ul>
        <div class="mt-4 border-t pt-4 space-y-1 text-sm">
            <div class="flex justify-between"><span>Total</span><strong>${{ number_format($order->total, 2) }}</strong></div>
            <div class="flex justify-between"><span>Déjà payé</span><span>${{ number_format($order->paid_amount, 2) }}</span></div>
            <div class="flex justify-between text-lg"><span>Reste</span><strong>${{ number_format($order->remainingBalance(), 2) }}</strong></div>
            <p class="text-xs text-stone-500">1 {{ $settings->currency_primary }} = {{ $settings->exchange_rate }} {{ $settings->currency_secondary }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pos.pay', $order) }}" class="erp-card space-y-4 p-5" id="payForm">
        @csrf
        <h2 class="font-display text-xl">Paiement</h2>
        <div>
            <label class="text-sm font-medium">Remise (USD)</label>
            <input type="number" step="0.01" name="discount_amount" value="0" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div id="payLines" class="space-y-3"></div>
        <button type="button" id="addPay" class="rounded-xl border border-stone-300 px-3 py-2 text-sm">+ Paiement mixte</button>
        <button class="block w-full rounded-xl bg-lime-800 py-3 text-white">Valider le paiement</button>
        <p class="text-xs text-stone-500">Mobile Money enregistré localement (structure prête pour API opérateurs).</p>
    </form>
</div>

<template id="payTpl">
    <div class="grid gap-2 rounded-xl bg-stone-50 p-3 sm:grid-cols-4">
        <select name="payments[IDX][method]" class="rounded-lg border border-stone-300 px-2 py-2">
            @foreach ($methods as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </select>
        <input type="number" step="0.01" name="payments[IDX][amount]" value="{{ $order->remainingBalance() }}" class="rounded-lg border border-stone-300 px-2 py-2" required>
        <select name="payments[IDX][currency]" class="rounded-lg border border-stone-300 px-2 py-2">
            <option value="{{ $settings->currency_primary }}">{{ $settings->currency_primary }}</option>
            <option value="{{ $settings->currency_secondary }}">{{ $settings->currency_secondary }}</option>
        </select>
        <input name="payments[IDX][reference]" placeholder="Réf. Mobile Money" class="rounded-lg border border-stone-300 px-2 py-2">
    </div>
</template>
@endsection

@push('scripts')
<script>
let i = 0;
const box = document.getElementById('payLines');
const tpl = document.getElementById('payTpl').innerHTML;
function add() {
    const wrap = document.createElement('div');
    wrap.innerHTML = tpl.replaceAll('IDX', i++);
    box.appendChild(wrap.firstElementChild);
}
document.getElementById('addPay').onclick = add;
add();
</script>
@endpush
