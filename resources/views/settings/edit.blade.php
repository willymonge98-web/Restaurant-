@extends('layouts.app')
@section('title', 'Paramètres')
@section('subtitle', 'Identité du restaurant et devises')
@section('content')
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="erp-card max-w-3xl p-6 space-y-4">
    @csrf @method('PUT')
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Nom du restaurant</label>
            <input name="name" value="{{ old('name', $settings->name) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Adresse</label>
            <input name="address" value="{{ old('address', $settings->address) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Téléphone</label>
            <input name="phone" value="{{ old('phone', $settings->phone) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $settings->email) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Devise principale</label>
            <input name="currency_primary" value="{{ old('currency_primary', $settings->currency_primary) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium">Devise secondaire</label>
            <input name="currency_secondary" value="{{ old('currency_secondary', $settings->currency_secondary) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium">Taux de change (1 {{ $settings->currency_primary }} = ? {{ $settings->currency_secondary }})</label>
            <input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', $settings->exchange_rate) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2" required>
        </div>
        <div>
            <label class="text-sm font-medium">TVA (%)</label>
            <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Libellé taxe</label>
            <input name="tax_label" value="{{ old('tax_label', $settings->tax_label) }}" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label class="text-sm font-medium">Logo</label>
            <input type="file" name="logo" accept="image/*" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">
            @if ($settings->logo_path)
                <img src="{{ asset('storage/'.$settings->logo_path) }}" alt="Logo" class="mt-2 h-16 object-contain">
            @endif
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-medium">Pied de ticket</label>
            <textarea name="ticket_footer" rows="3" class="mt-1 w-full rounded-xl border border-stone-300 px-3 py-2">{{ old('ticket_footer', $settings->ticket_footer) }}</textarea>
        </div>
    </div>
    <button class="rounded-xl bg-lime-800 px-4 py-2.5 text-white">Enregistrer</button>
</form>
@endsection
