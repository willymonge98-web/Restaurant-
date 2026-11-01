@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('subtitle', 'Vue du jour et tendances')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 animate-fade-up">
    <div class="stat-card">
        <p class="text-sm text-stone-500">CA du jour</p>
        <p class="mt-2 font-display text-3xl">${{ number_format($dayRevenue, 2) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-sm text-stone-500">Ventes du mois</p>
        <p class="mt-2 font-display text-3xl">${{ number_format($monthSales, 2) }}</p>
        <p class="mt-1 text-xs text-stone-500">Coût matières: ${{ number_format($monthCost, 2) }} · Marge brute: ${{ number_format($monthSales - $monthCost, 2) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-sm text-stone-500">Commandes aujourd'hui</p>
        <p class="mt-2 font-display text-3xl">{{ $ordersToday }}</p>
    </div>
    <div class="stat-card">
        <p class="text-sm text-stone-500">Alertes stock</p>
        <p class="mt-2 font-display text-3xl">{{ $lowStockIngredients->count() + $lowStockProducts->count() }}</p>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <div class="erp-card xl:col-span-2 p-5">
        <h2 class="font-display text-xl">Ventes 7 jours</h2>
        <canvas id="salesChart" class="mt-4 max-h-72"></canvas>
    </div>
    <div class="erp-card p-5">
        <h2 class="font-display text-xl">Statuts du jour</h2>
        <ul class="mt-4 space-y-2 text-sm">
            @forelse ($statusCounts as $status => $count)
                <li class="flex justify-between rounded-lg bg-stone-50 px-3 py-2">
                    <span>{{ \App\Models\Order::statusLabels()[$status] ?? $status }}</span>
                    <strong>{{ $count }}</strong>
                </li>
            @empty
                <li class="text-stone-500">Aucune commande aujourd'hui.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="erp-card p-5">
        <h2 class="font-display text-xl">Stock bas — ingrédients</h2>
        <ul class="mt-3 divide-y divide-stone-100 text-sm">
            @forelse ($lowStockIngredients as $ing)
                <li class="flex justify-between py-2">
                    <span>{{ $ing->name }}</span>
                    <span class="text-amber-700">{{ $ing->stock_qty }} {{ $ing->unit }}</span>
                </li>
            @empty
                <li class="py-2 text-stone-500">RAS</li>
            @endforelse
        </ul>
    </div>
    <div class="erp-card p-5">
        <h2 class="font-display text-xl">Stock bas — produits suivis</h2>
        <ul class="mt-3 divide-y divide-stone-100 text-sm">
            @forelse ($lowStockProducts as $p)
                <li class="flex justify-between py-2">
                    <span>{{ $p->name }}</span>
                    <span class="text-amber-700">{{ $p->stock_qty }}</span>
                </li>
            @empty
                <li class="py-2 text-stone-500">RAS</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('salesChart');
    if (!ctx || !window.Chart) return;
    const data = @json($chartDays);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'CA (USD)',
                data: data.map(d => d.total),
                borderColor: '#3f6212',
                backgroundColor: 'rgba(63,98,18,.15)',
                fill: true,
                tension: .35,
            }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush
