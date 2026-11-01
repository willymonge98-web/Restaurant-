@extends('layouts.app')
@section('title', 'Cuisine')
@section('subtitle', 'File de préparation')
@section('content')
<div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
@forelse ($orders as $order)
    <div class="erp-card p-5 animate-fade-up">
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="font-display text-xl">{{ $order->number }}</p>
                <p class="text-sm text-stone-500">
                    {{ \App\Models\Order::typeLabels()[$order->type] }}
                    @if($order->diningTable) · {{ $order->diningTable->name }} @endif
                    · {{ $order->created_at->format('H:i') }}
                </p>
            </div>
            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs text-amber-900">{{ \App\Models\Order::statusLabels()[$order->status] }}</span>
        </div>
        <ul class="mt-4 space-y-3">
            @foreach ($order->items->where('kitchen_status', '!=', 'cancelled') as $item)
                <li class="rounded-xl bg-stone-50 p-3">
                    <div class="flex justify-between gap-2">
                        <div>
                            <strong>{{ $item->quantity }}× {{ $item->product_name }}</strong>
                            @if($item->notes)<div class="text-xs text-rose-700">{{ $item->notes }}</div>@endif
                        </div>
                        <form method="POST" action="{{ route('kitchen.items.update', $item) }}" class="flex gap-1">
                            @csrf @method('PATCH')
                            <select name="kitchen_status" class="rounded-lg border border-stone-300 px-2 py-1 text-xs">
                                @foreach (['pending'=>'En attente','preparing'=>'En cours','ready'=>'Prêt','served'=>'Servi'] as $k=>$v)
                                    <option value="{{ $k }}" @selected($item->kitchen_status===$k)>{{ $v }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-lg bg-stone-900 px-2 text-xs text-white">OK</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
        <form method="POST" action="{{ route('kitchen.ready', $order) }}" class="mt-4">
            @csrf
            <button class="w-full rounded-xl bg-lime-800 py-2 text-white">Tout marquer prêt</button>
        </form>
    </div>
@empty
    <div class="erp-card p-8 text-stone-500 lg:col-span-3">Aucune commande en cuisine.</div>
@endforelse
</div>
@endsection
