@extends('layouts.app')
@section('title', 'Recettes')
@section('subtitle', 'Lien plat → ingrédients (déduction stock à la vente)')
@section('content')
<div class="grid gap-6 xl:grid-cols-2">
    <form method="POST" action="{{ route('recipes.store') }}" class="erp-card space-y-3 p-5" id="recipeForm">
        @csrf
        <h2 class="font-display text-xl">Nouvelle recette</h2>
        <select name="product_id" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
            <option value="">Produit…</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
        <input name="name" placeholder="Nom recette (optionnel)" class="w-full rounded-xl border border-stone-300 px-3 py-2">
        <div id="recipeLines" class="space-y-2"></div>
        <button type="button" id="addIngredient" class="rounded-xl border border-stone-300 px-3 py-2 text-sm">+ Ingrédient</button>
        <button class="block rounded-xl bg-lime-800 px-4 py-2 text-white">Créer</button>
    </form>

    <div class="space-y-4">
        @foreach ($recipes as $recipe)
            <div class="erp-card p-5">
                <div class="flex justify-between gap-2">
                    <div>
                        <p class="font-display text-lg">{{ $recipe->product->name }}</p>
                        <p class="text-sm text-stone-500">Coût calculé: ${{ number_format($recipe->computeCost(), 2) }} · Prix: ${{ number_format($recipe->product->price, 2) }}</p>
                    </div>
                    <span class="text-xs {{ $recipe->is_active ? 'text-lime-800' : 'text-stone-400' }}">{{ $recipe->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <ul class="mt-3 text-sm space-y-1">
                    @foreach ($recipe->items as $item)
                        <li>{{ $item->quantity }} {{ $item->ingredient->unit }} × {{ $item->ingredient->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>

<template id="ingTpl">
    <div class="grid grid-cols-[2fr_1fr] gap-2">
        <select name="items[IDX][ingredient_id]" class="rounded-xl border border-stone-300 px-3 py-2" required>
            @foreach ($ingredients as $ing)
                <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
            @endforeach
        </select>
        <input type="number" step="0.0001" name="items[IDX][quantity]" placeholder="Qté" class="rounded-xl border border-stone-300 px-3 py-2" required>
    </div>
</template>
@endsection

@push('scripts')
<script>
let i=0; const box=document.getElementById('recipeLines'); const tpl=document.getElementById('ingTpl').innerHTML;
function add(){ const w=document.createElement('div'); w.innerHTML=tpl.replaceAll('IDX', i++); box.appendChild(w.firstElementChild); }
document.getElementById('addIngredient').onclick=add; add();
</script>
@endpush
