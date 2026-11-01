@extends('layouts.app')
@section('title', 'Stock')
@section('subtitle', 'Ingrédients et mouvements')
@section('content')
<div class="grid gap-6 xl:grid-cols-3">
    <form method="POST" action="{{ route('stock.ingredients.store') }}" class="erp-card h-fit space-y-3 p-5">
        @csrf
        <h2 class="font-display text-lg">Nouvel ingrédient</h2>
        <input name="name" placeholder="Nom" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
        <input name="sku" placeholder="SKU" class="w-full rounded-xl border border-stone-300 px-3 py-2">
        <input name="unit" placeholder="Unité (g, ml, unit…)" value="g" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
        <input type="number" step="0.001" name="stock_qty" placeholder="Stock initial" class="w-full rounded-xl border border-stone-300 px-3 py-2">
        <input type="number" step="0.001" name="stock_alert" placeholder="Seuil alerte" class="w-full rounded-xl border border-stone-300 px-3 py-2">
        <input type="number" step="0.0001" name="unit_cost" placeholder="Coût unitaire" class="w-full rounded-xl border border-stone-300 px-3 py-2">
        <button class="rounded-xl bg-lime-800 px-4 py-2 text-white">Ajouter</button>
    </form>

    <div class="erp-card xl:col-span-2 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-50 text-left">
                <tr>
                    <th class="px-4 py-3">Ingrédient</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Coût</th>
                    <th class="px-4 py-3">Ajustement</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($ingredients as $ingredient)
                <tr class="border-t {{ $ingredient->isLowStock() ? 'bg-amber-50' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $ingredient->name }}</div>
                        <div class="text-xs text-stone-500">{{ $ingredient->sku }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $ingredient->stock_qty }} {{ $ingredient->unit }}</td>
                    <td class="px-4 py-3">${{ number_format($ingredient->unit_cost, 4) }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('stock.adjust', $ingredient) }}" class="flex flex-wrap gap-1">
                            @csrf
                            <select name="type" class="rounded-lg border border-stone-300 px-2 py-1 text-xs">
                                <option value="in">Entrée</option>
                                <option value="out">Sortie</option>
                                <option value="loss">Perte</option>
                                <option value="adjust">Inventaire</option>
                            </select>
                            <input type="number" step="0.001" name="quantity" class="w-20 rounded-lg border border-stone-300 px-2 py-1 text-xs" required>
                            <button class="rounded-lg bg-stone-900 px-2 py-1 text-xs text-white">OK</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $ingredients->links() }}</div>
    </div>
</div>

<div class="erp-card mt-6 p-5">
    <h2 class="font-display text-lg">Derniers mouvements</h2>
    <ul class="mt-3 divide-y text-sm">
        @foreach ($movements as $m)
            <li class="flex justify-between py-2">
                <span>{{ class_basename($m->movable_type) }} #{{ $m->movable_id }} · {{ $m->type }} · {{ $m->quantity }}</span>
                <span class="text-stone-500">{{ $m->created_at->format('d/m H:i') }}</span>
            </li>
        @endforeach
    </ul>
</div>
@endsection
