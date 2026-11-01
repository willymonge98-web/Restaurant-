@extends('layouts.app')
@section('title', 'Plan de salle')
@section('subtitle', 'Libre / Occupée / Réservée / En nettoyage')
@section('content')
@php
$colors = [
    'libre' => 'bg-lime-100 border-lime-300 text-lime-950',
    'occupee' => 'bg-rose-100 border-rose-300 text-rose-950',
    'reservee' => 'bg-amber-100 border-amber-300 text-amber-950',
    'en_nettoyage' => 'bg-sky-100 border-sky-300 text-sky-950',
];
@endphp
<div class="mb-6 flex flex-wrap gap-3 text-sm">
    @foreach ($statuses as $key => $label)
        <span class="rounded-full border px-3 py-1 {{ $colors[$key] }}">{{ $label }}</span>
    @endforeach
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($tables as $table)
        <div class="table-tile rounded-2xl border p-4 {{ $colors[$table->status] ?? 'bg-white' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-display text-xl">{{ $table->name }}</p>
                    <p class="text-sm opacity-80">{{ $table->code }} · {{ $table->capacity }} places @if($table->zone)· {{ $table->zone }}@endif</p>
                </div>
                <span class="text-xs font-semibold uppercase">{{ $statuses[$table->status] ?? $table->status }}</span>
            </div>
            <form method="POST" action="{{ route('tables.status', $table) }}" class="mt-4 flex gap-2">
                @csrf @method('PATCH')
                <select name="status" class="flex-1 rounded-lg border border-black/10 bg-white/70 px-2 py-1.5 text-sm">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($table->status===$key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-stone-900 px-3 py-1.5 text-sm text-white">OK</button>
            </form>
        </div>
    @endforeach
</div>

<form method="POST" action="{{ route('tables.store') }}" class="erp-card mt-8 grid max-w-3xl gap-3 p-5 sm:grid-cols-4">
    @csrf
    <h2 class="font-display text-lg sm:col-span-4">Ajouter une table</h2>
    <input name="name" placeholder="Nom" class="rounded-xl border border-stone-300 px-3 py-2" required>
    <input name="code" placeholder="Code" class="rounded-xl border border-stone-300 px-3 py-2" required>
    <input type="number" name="capacity" placeholder="Places" value="4" class="rounded-xl border border-stone-300 px-3 py-2" required>
    <input name="zone" placeholder="Zone" class="rounded-xl border border-stone-300 px-3 py-2">
    <button class="rounded-xl bg-lime-800 px-4 py-2 text-white sm:col-span-4 sm:w-fit">Créer</button>
</form>
@endsection
