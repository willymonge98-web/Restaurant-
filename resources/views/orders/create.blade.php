@extends('layouts.app')
@section('title', 'Nouvelle commande')
@section('content')
@php
    $settings = \App\Models\RestaurantSetting::current();
    $rate = (float) $settings->exchange_rate;
@endphp
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
        <h2 class="font-display text-xl">Carte — choisissez les plats</h2>
        <p class="text-sm text-stone-500">Touchez un plat pour l’ajouter à la commande</p>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" id="menuGrid">
            @foreach ($products as $product)
                <button type="button"
                    class="menu-item overflow-hidden rounded-2xl border border-stone-200 bg-white text-left transition hover:-translate-y-0.5 hover:border-lime-700"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}">
                    <div class="aspect-[4/3] bg-stone-100">
                        @if ($product->imageUrl())
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover" loading="lazy">
                        @endif
                    </div>
                    <div class="p-3">
                        <div class="text-[11px] uppercase tracking-wide text-stone-500">{{ $product->category?->name }}</div>
                        <div class="font-medium leading-tight">{{ $product->name }}</div>
                        <div class="mt-1 text-sm text-lime-900">${{ number_format($product->price, 2) }}
                            <span class="text-xs text-stone-500">· {{ number_format($product->priceCdf($rate), 0, ',', ' ') }} CDF</span>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <div class="erp-card p-5">
        <h2 class="font-display text-xl">Panier</h2>
        <div id="cart" class="mt-3 space-y-2 text-sm"></div>
        <div id="hiddenItems"></div>
        <p id="emptyCart" class="mt-2 text-stone-500">Aucun article pour l’instant.</p>
    </div>

    <button class="rounded-xl bg-lime-800 px-5 py-3 text-white">Créer la commande</button>
</form>
@endsection

@push('scripts')
<script>
const cart = new Map();
const cartEl = document.getElementById('cart');
const hidden = document.getElementById('hiddenItems');
const empty = document.getElementById('emptyCart');

function renderCart() {
    cartEl.innerHTML = '';
    hidden.innerHTML = '';
    let i = 0;
    if (cart.size === 0) {
        empty.style.display = '';
        return;
    }
    empty.style.display = 'none';
    for (const [id, row] of cart.entries()) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between rounded-xl bg-stone-50 px-3 py-2';
        div.innerHTML = `<span>${row.qty}× ${row.name}</span><span>$${(row.qty*row.price).toFixed(2)} <button type="button" data-remove="${id}" class="ml-2 text-rose-700">retirer</button></span>`;
        cartEl.appendChild(div);
        hidden.insertAdjacentHTML('beforeend', `
            <input type="hidden" name="items[${i}][product_id]" value="${id}">
            <input type="hidden" name="items[${i}][quantity]" value="${row.qty}">
        `);
        i++;
    }
    cartEl.querySelectorAll('[data-remove]').forEach(btn => {
        btn.onclick = () => { cart.delete(btn.dataset.remove); renderCart(); };
    });
}

document.querySelectorAll('.menu-item').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const existing = cart.get(id) || { name: btn.dataset.name, price: parseFloat(btn.dataset.price), qty: 0 };
        existing.qty += 1;
        cart.set(id, existing);
        renderCart();
    });
});

const type = document.getElementById('orderType');
function syncType() {
    document.getElementById('tableField').style.display = type.value === 'dine_in' ? '' : 'none';
    document.getElementById('addressField').style.display = type.value === 'delivery' ? '' : 'none';
}
type.onchange = syncType; syncType();
</script>
@endpush
