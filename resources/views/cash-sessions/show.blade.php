@extends('layouts.app')
@section('title', 'Session caisse #'.$session->id)
@section('content')
<div class="erp-card max-w-3xl space-y-4 p-6">
    <div class="grid gap-3 sm:grid-cols-2 text-sm">
        <div><span class="text-stone-500">Caissier</span><div class="font-medium">{{ $session->user->name }}</div></div>
        <div><span class="text-stone-500">Statut</span><div class="font-medium">{{ $session->status }}</div></div>
        <div><span class="text-stone-500">Ouverture</span><div>{{ $session->opened_at->format('d/m/Y H:i') }} · fond ${{ number_format($session->opening_float, 2) }}</div></div>
        <div><span class="text-stone-500">Clôture</span><div>{{ $session->closed_at?->format('d/m/Y H:i') ?? '—' }}</div></div>
        <div><span class="text-stone-500">Théorique espèces</span><div>${{ number_format($session->theoretical_cash ?? 0, 2) }}</div></div>
        <div><span class="text-stone-500">Compté / Écart</span><div>${{ number_format($session->closing_counted ?? 0, 2) }} / ${{ number_format($session->difference ?? 0, 2) }}</div></div>
        <div class="sm:col-span-2"><span class="text-stone-500">Total ventes session</span><div class="font-display text-2xl">${{ number_format($session->total_sales, 2) }}</div></div>
    </div>

    @if ($session->totals_by_method)
        <div>
            <h2 class="font-display text-lg">Par méthode</h2>
            <ul class="mt-2 space-y-1 text-sm">
                @foreach ($session->totals_by_method as $method => $amount)
                    <li class="flex justify-between rounded-lg bg-stone-50 px-3 py-2">
                        <span>{{ \App\Models\Payment::METHODS[$method] ?? $method }}</span>
                        <span>${{ number_format($amount, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <h2 class="font-display text-lg">Paiements</h2>
        <ul class="mt-2 divide-y text-sm">
            @foreach ($session->payments as $payment)
                <li class="flex justify-between py-2">
                    <span>{{ $payment->order?->number }} · {{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</span>
                    <span>${{ number_format($payment->amount_usd ?? $payment->amount, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
