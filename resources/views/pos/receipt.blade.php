@extends('layouts.app')
@section('title', 'Ticket '.$order->number)
@section('content')
<div class="erp-card mx-auto max-w-md p-6 text-center">
    <p class="font-display text-3xl">{{ $settings->name }}</p>
    <p class="mt-1 text-sm text-stone-500">{{ $settings->address }} · {{ $settings->phone }}</p>
    <hr class="my-4">
    <p class="font-medium">{{ $order->number }}</p>
    <p class="text-sm">{{ $order->validated_at?->format('d/m/Y H:i') }} · {{ $order->cashier?->name }}</p>
    <ul class="mt-4 space-y-1 text-left text-sm">
        @foreach ($order->items as $item)
            <li class="flex justify-between"><span>{{ $item->quantity }}× {{ $item->product_name }}</span><span>${{ number_format($item->line_total, 2) }}</span></li>
        @endforeach
    </ul>
    <div class="mt-4 border-t pt-3 text-left text-sm space-y-1">
        <div class="flex justify-between"><span>Total</span><strong>${{ number_format($order->total, 2) }}</strong></div>
        @foreach ($order->payments as $payment)
            <div class="flex justify-between text-stone-600">
                <span>{{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}</span>
                <span>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
            </div>
        @endforeach
    </div>
    <p class="mt-6 text-sm text-stone-500">{{ $settings->ticket_footer }}</p>
    <a href="{{ route('pos.receipt.pdf', $order) }}" class="mt-6 inline-block rounded-xl bg-lime-800 px-4 py-2 text-white">PDF</a>
</div>
@endsection
