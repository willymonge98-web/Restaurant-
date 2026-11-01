@extends('layouts.app')
@section('title', 'Point de vente')
@section('subtitle', 'Encaissement des commandes ouvertes')
@section('content')
@if (!$session)
    <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-amber-950">
        Aucune session de caisse ouverte.
        <a href="{{ route('cash-sessions.index') }}" class="underline">Ouvrir la caisse</a>
    </div>
@else
    <div class="mb-6 rounded-xl border border-lime-300 bg-lime-50 px-4 py-3 text-lime-950">
        Session #{{ $session->id }} ouverte depuis {{ $session->opened_at->format('d/m H:i') }} · fond: ${{ number_format($session->opening_float, 2) }}
    </div>
@endif

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
@forelse ($orders as $order)
    <a href="{{ route('pos.checkout', $order) }}" class="erp-card block p-5 transition hover:-translate-y-0.5">
        <div class="flex justify-between">
            <p class="font-display text-xl">{{ $order->number }}</p>
            <span class="text-sm text-stone-500">{{ \App\Models\Order::statusLabels()[$order->status] }}</span>
        </div>
        <p class="mt-2 text-sm">{{ $order->diningTable?->name ?? \App\Models\Order::typeLabels()[$order->type] }}</p>
        <p class="mt-4 font-semibold text-2xl">${{ number_format($order->total, 2) }}</p>
        <p class="text-sm text-stone-500">Reste: ${{ number_format($order->remainingBalance(), 2) }}</p>
    </a>
@empty
    <div class="erp-card p-8 text-stone-500 md:col-span-2 xl:col-span-3">Aucune commande à encaisser.</div>
@endforelse
</div>
@endsection
