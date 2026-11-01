<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — LÉNA RESTAURANT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased">
<div class="relative min-h-screen overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1600&q=80')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-stone-950/85 via-stone-900/75 to-lime-950/60"></div>

    <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12">
        <div class="grid w-full gap-10 lg:grid-cols-2 lg:items-end">
            <div class="text-white animate-fade-up">
                <p class="font-display text-5xl sm:text-6xl leading-none">LÉNA RESTAURANT</p>
                <p class="mt-4 max-w-md text-lg text-stone-200">Système de gestion ERP / POS — commandes, cuisine, stock et caisse.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="erp-card max-w-md animate-fade-up p-8" style="animation-delay:.1s">
                @csrf
                <h1 class="font-display text-2xl">Connexion</h1>
                <p class="mt-1 text-sm text-stone-500">Accédez à votre espace restaurant</p>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-800">{{ $errors->first() }}</div>
                @endif

                <label class="mt-6 block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', 'admin@lena.restaurant') }}" required
                       class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 outline-none focus:border-lime-700">

                <label class="mt-4 block text-sm font-medium">Mot de passe</label>
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 outline-none focus:border-lime-700">

                <label class="mt-4 flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox" name="remember" class="rounded border-stone-300"> Se souvenir de moi
                </label>

                <button class="mt-6 w-full rounded-xl bg-lime-800 px-4 py-3 font-semibold text-white hover:bg-lime-700">Se connecter</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
