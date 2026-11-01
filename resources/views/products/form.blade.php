@extends('layouts.app')
@section('title', $product->exists ? 'Modifier produit' : 'Nouveau produit')
@section('content')
<form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" class="erp-card max-w-2xl space-y-4 p-6">
    @csrf
    @if ($product->exists) @method('PUT') @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Nom</label>
            <input name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium">Catégorie</label>
            <select name="category_id" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id)==$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Type</label>
            <select name="type" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
                <option value="food" @selected(old('type', $product->type)==='food')>Nourriture</option>
                <option value="drink" @selected(old('type', $product->type)==='drink')>Boisson</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Prix de vente</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium">Coût (manuel / recette)</label>
            <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">SKU</label>
            <input name="sku" value="{{ old('sku', $product->sku) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Stock (si suivi unitaire)</label>
            <input type="number" step="0.001" name="stock_qty" value="{{ old('stock_qty', $product->stock_qty) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Description</label>
            <textarea name="description" rows="3" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">{{ old('description', $product->description) }}</textarea>
        </div>
    </div>
    <div class="flex flex-wrap gap-4 text-sm">
        <label class="flex items-center gap-2"><input type="checkbox" name="track_stock" value="1" @checked(old('track_stock', $product->track_stock))> Suivre le stock produit</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> Actif</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_available" value="1" @checked(old('is_available', $product->is_available ?? true))> Disponible</label>
    </div>
    <button class="rounded-xl bg-lime-800 px-4 py-2.5 text-white">Enregistrer</button>
</form>
@endsection
