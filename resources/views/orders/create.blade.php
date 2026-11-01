@extends('layouts.app')
@section('title', 'Nouvelle commande')
@section('content')
<form method="POST" action="{{ route('orders.store') }}" class="space-y-6" id="orderForm">
    @csrf
    <div class="erp-card grid gap-4 p-5 sm:grid-cols-2">
        <div>
            <label class="text-sm font-medium">Type</label>
            <select name="type" id="orderType" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
                @foreach ($types as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div id="tableField">
            <label class="text-sm font-medium">Table</label>
            <select name="dining_table_id" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
                <option value="">—</option>
                @foreach ($tables as $table)
                    <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->status }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Client</label>
            <input name="customer_name" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Téléphone</label>
            <input name="customer_phone" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div class="sm:col-span-2" id="addressField" style="display:none">
            <label class="text-sm font-medium">Adresse de livraison</label>
            <input name="delivery_address" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Couverts</label>
            <input type="number" name="covers" value="2" min="1" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Notes</label>
            <input name="notes" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
    </div>

    <div class="erp-card p-5">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-xl">Articles</h2>
            <button type="button" id="addLine" class="rounded-xl bg-stone-900 px-3 py-2 text-sm text-white">+ Ligne</button>
        </div>
        <div id="lines" class="mt-4 space-y-3"></div>
    </div>

    <button class="rounded-xl bg-lime-800 px-5 py-3 text-white">Créer la commande</button>
</form>

<template id="lineTpl">
    <div class="grid gap-2 rounded-xl bg-stone-50 p-3 sm:grid-cols-[2fr_1fr_1.5fr_auto]">
        <select name="items[IDX][product_id]" class="rounded-lg border border-stone-300 px-2 py-2" required>
            <option value="">Produit…</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} — ${{ number_format($product->price, 2) }}</option>
            @endforeach
        </select>
        <input type="number" name="items[IDX][quantity]" value="1" min="1" class="rounded-lg border border-stone-300 px-2 py-2" required>
        <input name="items[IDX][notes]" placeholder="Note cuisine" class="rounded-lg border border-stone-300 px-2 py-2">
        <button type="button" class="removeLine rounded-lg border border-rose-200 px-3 text-rose-700">×</button>
    </div>
</template>
@endsection

@push('scripts')
<script>
let idx = 0;
const lines = document.getElementById('lines');
const tpl = document.getElementById('lineTpl').innerHTML;
function addLine() {
    const html = tpl.replaceAll('IDX', idx++);
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    const el = wrap.firstElementChild;
    el.querySelector('.removeLine').onclick = () => el.remove();
    lines.appendChild(el);
}
document.getElementById('addLine').onclick = addLine;
addLine();
const type = document.getElementById('orderType');
function syncType() {
    document.getElementById('tableField').style.display = type.value === 'dine_in' ? '' : 'none';
    document.getElementById('addressField').style.display = type.value === 'delivery' ? '' : 'none';
}
type.onchange = syncType; syncType();
</script>
@endpush
