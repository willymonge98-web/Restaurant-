@extends('layouts.app')
@section('title', 'Sessions de caisse')
@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="erp-card p-5 h-fit space-y-4">
        @if ($current)
            <h2 class="font-display text-xl">Session ouverte #{{ $current->id }}</h2>
            <p class="text-sm">Ouverte le {{ $current->opened_at->format('d/m/Y H:i') }}</p>
            <p>Fond de caisse: ${{ number_format($current->opening_float, 2) }}</p>
            <form method="POST" action="{{ route('cash-sessions.close', $current) }}" class="space-y-3">
                @csrf
                <label class="text-sm font-medium">Espèces comptées (théorique vs réel)</label>
                <input type="number" step="0.01" name="closing_counted" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
                <textarea name="notes" rows="2" class="w-full rounded-xl border border-stone-300 px-3 py-2" placeholder="Notes de clôture"></textarea>
                <button class="rounded-xl bg-rose-800 px-4 py-2 text-white">Clôturer</button>
            </form>
        @else
            <h2 class="font-display text-xl">Ouvrir la caisse</h2>
            <form method="POST" action="{{ route('cash-sessions.open') }}" class="space-y-3">
                @csrf
                <label class="text-sm font-medium">Fond de caisse (ouverture)</label>
                <input type="number" step="0.01" name="opening_float" value="50" class="w-full rounded-xl border border-stone-300 px-3 py-2" required>
                <textarea name="notes" rows="2" class="w-full rounded-xl border border-stone-300 px-3 py-2"></textarea>
                <button class="rounded-xl bg-lime-800 px-4 py-2 text-white">Ouvrir</button>
            </form>
        @endif
    </div>

    <div class="erp-card lg:col-span-2 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-50 text-left">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Caissier</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Théorique</th>
                    <th class="px-4 py-3">Compté</th>
                    <th class="px-4 py-3">Écart</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($sessions as $session)
                <tr class="border-t">
                    <td class="px-4 py-3"><a class="text-lime-800" href="{{ route('cash-sessions.show', $session) }}">{{ $session->id }}</a></td>
                    <td class="px-4 py-3">{{ $session->user->name }}</td>
                    <td class="px-4 py-3">{{ $session->status === 'open' ? 'Ouverte' : 'Clôturée' }}</td>
                    <td class="px-4 py-3">{{ $session->theoretical_cash !== null ? '$'.number_format($session->theoretical_cash, 2) : '—' }}</td>
                    <td class="px-4 py-3">{{ $session->closing_counted !== null ? '$'.number_format($session->closing_counted, 2) : '—' }}</td>
                    <td class="px-4 py-3 {{ ($session->difference ?? 0) < 0 ? 'text-rose-700' : '' }}">
                        {{ $session->difference !== null ? '$'.number_format($session->difference, 2) : '—' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection
