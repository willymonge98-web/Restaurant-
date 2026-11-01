@extends('layouts.app')
@section('title', 'Commandes')
@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form class="flex flex-wrap gap-2">
        <select name="status" class="rounded-xl border border-stone-300 px-3 py-2">
            <option value="">Tous statuts</option>
            @foreach ($statuses as $k => $v)
                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <select name="type" class="rounded-xl border border-stone-300 px-3 py-2">
            <option value="">Tous types</option>
            @foreach ($types as $k => $v)
                <option value="{{ $k }}" @selected(request('type')===$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-stone-900 px-3 py-2 text-white">Filtrer</button>
    </form>
    <a href="{{ route('orders.create') }}" class="rounded-xl bg-lime-800 px-4 py-2 text-white">Nouvelle commande</a>
</div>
<div class="erp-card overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-stone-50 text-left">
            <tr>
                <th class="px-4 py-3">N°</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Table</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Créée</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($orders as $order)
            <tr class="border-t hover:bg-stone-50/80">
                <td class="px-4 py-3"><a class="font-medium text-lime-800" href="{{ route('orders.show', $order) }}">{{ $order->number }}</a></td>
                <td class="px-4 py-3">{{ $types[$order->type] ?? $order->type }}</td>
                <td class="px-4 py-3">{{ $order->diningTable?->name ?? '—' }}</td>
                <td class="px-4 py-3">{{ $statuses[$order->status] ?? $order->status }}</td>
                <td class="px-4 py-3">${{ number_format($order->total, 2) }}</td>
                <td class="px-4 py-3">{{ $order->created_at->format('d/m H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $orders->links() }}</div>
</div>
@endsection
