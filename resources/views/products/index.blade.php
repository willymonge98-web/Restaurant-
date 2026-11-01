@extends('layouts.app')
@section('title', 'Produits')
@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="Rechercher…" class="rounded-xl border border-stone-300 px-3 py-2">
        <select name="type" class="rounded-xl border border-stone-300 px-3 py-2">
            <option value="">Tous</option>
            <option value="food" @selected(request('type')==='food')>Plats</option>
            <option value="drink" @selected(request('type')==='drink')>Boissons</option>
        </select>
        <button class="rounded-xl bg-stone-900 px-3 py-2 text-white">Filtrer</button>
    </form>
    <a href="{{ route('products.create') }}" class="rounded-xl bg-lime-800 px-4 py-2 text-white">Nouveau produit</a>
</div>
<div class="erp-card overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-50 text-left">
            <tr>
                <th class="px-4 py-3">Produit</th>
                <th class="px-4 py-3">Catégorie</th>
                <th class="px-4 py-3">Prix</th>
                <th class="px-4 py-3">Coût</th>
                <th class="px-4 py-3">Marge</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr class="border-t">
                <td class="px-4 py-3">
                    <div class="font-medium">{{ $product->name }}</div>
                    <div class="text-xs text-stone-500">{{ $product->type }} @if($product->recipe) · recette @endif</div>
                </td>
                <td class="px-4 py-3">{{ $product->category?->name }}</td>
                <td class="px-4 py-3">${{ number_format($product->price, 2) }}</td>
                <td class="px-4 py-3">${{ number_format($product->cost_price, 2) }}</td>
                <td class="px-4 py-3">{{ $product->marginPercent() }}%</td>
                <td class="px-4 py-3 text-right"><a class="text-lime-800" href="{{ route('products.edit', $product) }}">Modifier</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $products->links() }}</div>
</div>
@endsection
