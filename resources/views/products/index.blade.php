@extends('layouts.app')
@section('title', 'Produits / Carte')
@section('subtitle', 'Plats, boissons et cuisine congolaise')
@section('content')
@php
    $settings = \App\Models\RestaurantSetting::current();
    $rate = (float) $settings->exchange_rate;
@endphp
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex flex-wrap gap-2">
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

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($products as $product)
        <article class="erp-card overflow-hidden">
            <div class="aspect-[4/3] bg-stone-200 overflow-hidden">
                @if ($product->imageUrl())
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover" loading="lazy">
                @else
                    <div class="flex h-full items-center justify-center text-stone-400 text-sm">Sans photo</div>
                @endif
            </div>
            <div class="p-4">
                <div class="text-xs uppercase tracking-wide text-stone-500">{{ $product->category?->name }}</div>
                <h3 class="mt-1 font-display text-lg leading-tight">{{ $product->name }}</h3>
                @if ($product->description)
                    <p class="mt-1 line-clamp-2 text-sm text-stone-600">{{ $product->description }}</p>
                @endif
                <div class="mt-3 flex items-end justify-between gap-2">
                    <div>
                        <div class="font-semibold text-lime-900">${{ number_format($product->price, 2) }}</div>
                        <div class="text-xs text-stone-500">{{ number_format($product->priceCdf($rate), 0, ',', ' ') }} CDF</div>
                    </div>
                    <a href="{{ route('products.edit', $product) }}" class="text-sm text-lime-800">Modifier</a>
                </div>
            </div>
        </article>
    @endforeach
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
