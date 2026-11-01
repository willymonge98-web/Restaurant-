@php
    $appSettings = $appSettings ?? \App\Models\RestaurantSetting::current();
    $nav = [
        ['route' => 'dashboard', 'label' => 'Tableau de bord', 'roles' => null],
        ['route' => 'orders.index', 'label' => 'Commandes', 'roles' => ['admin','manager','waiter','cashier']],
        ['route' => 'tables.index', 'label' => 'Tables', 'roles' => ['admin','manager','waiter','cashier']],
        ['route' => 'kitchen.index', 'label' => 'Cuisine', 'roles' => ['admin','manager','cook']],
        ['route' => 'pos.index', 'label' => 'Caisse / POS', 'roles' => ['admin','manager','cashier']],
        ['route' => 'cash-sessions.index', 'label' => 'Sessions caisse', 'roles' => ['admin','manager','cashier']],
        ['route' => 'products.index', 'label' => 'Produits', 'roles' => ['admin','manager','storekeeper']],
        ['route' => 'categories.index', 'label' => 'Catégories', 'roles' => ['admin','manager','storekeeper']],
        ['route' => 'recipes.index', 'label' => 'Recettes', 'roles' => ['admin','manager','storekeeper']],
        ['route' => 'stock.index', 'label' => 'Stock', 'roles' => ['admin','manager','storekeeper']],
        ['route' => 'users.index', 'label' => 'Utilisateurs', 'roles' => ['admin','manager']],
        ['route' => 'settings.edit', 'label' => 'Paramètres', 'roles' => ['admin','manager']],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') — {{ $appSettings->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
</head>
<body class="antialiased">
<div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
    <aside class="bg-stone-900 text-stone-100 lg:min-h-screen">
        <div class="px-6 py-7 border-b border-white/10">
            <p class="font-display text-2xl tracking-tight">{{ $appSettings->name }}</p>
            <p class="text-xs text-stone-400 mt-1">ERP / POS Restaurant</p>
        </div>
        <nav class="p-4 space-y-1">
            @foreach ($nav as $item)
                @if ($item['roles'] === null || auth()->user()->canAccess(...$item['roles']))
                    <a href="{{ route($item['route']) }}"
                       class="block rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs(str_replace('.index','.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-lime-700 text-white' : 'hover:bg-white/5 text-stone-300' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-20 border-b border-stone-200/70 bg-[#f7f4ee]/90 backdrop-blur">
            <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-8">
                <div>
                    <h1 class="font-display text-xl sm:text-2xl">@yield('title', 'Tableau de bord')</h1>
                    <p class="text-sm text-stone-500">@yield('subtitle', 'Gestion restaurant')</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="hidden sm:block text-right">
                        <div class="font-medium">{{ auth()->user()->name }}</div>
                        <div class="text-stone-500">{{ auth()->user()->role?->label_fr ?? auth()->user()->role?->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-xl bg-stone-900 px-3 py-2 text-white hover:bg-stone-700">Déconnexion</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-lime-700/20 bg-lime-50 px-4 py-3 text-lime-900">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-700/20 bg-rose-50 px-4 py-3 text-rose-900">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
