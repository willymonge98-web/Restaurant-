@extends('layouts.app')
@section('title', 'Catégories')
@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <form method="POST" action="{{ route('categories.store') }}" class="erp-card h-fit space-y-3 p-5">
        @csrf
        <h2 class="font-display text-lg">Nouvelle catégorie</h2>
        <input name="name" placeholder="Nom" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
        <select name="type" class="w-full rounded-xl border border-stone-300 px-3 py-2">
            <option value="food">Nourriture</option>
            <option value="drink">Boisson</option>
        </select>
        <input type="number" name="sort_order" placeholder="Ordre" class="w-full rounded-xl border border-stone-300 px-3 py-2">
        <button class="rounded-xl bg-lime-800 px-4 py-2 text-white">Ajouter</button>
    </form>
    <div class="erp-card lg:col-span-2 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-50 text-left"><tr><th class="px-4 py-3">Nom</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Produits</th><th class="px-4 py-3">Actif</th></tr></thead>
            <tbody>
            @foreach ($categories as $category)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $category->name }}</td>
                    <td class="px-4 py-3">{{ $category->type === 'drink' ? 'Boisson' : 'Nourriture' }}</td>
                    <td class="px-4 py-3">{{ $category->products_count }}</td>
                    <td class="px-4 py-3">{{ $category->is_active ? 'Oui' : 'Non' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
