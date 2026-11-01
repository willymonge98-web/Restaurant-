<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1c1917">
    <title>Connexion — LÉNA RESTAURANT</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|source-sans-3:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --login-ink: #1c1917;
            --login-leaf: #3f6212;
            --login-leaf-deep: #2a450c;
            --login-cream: #f4efe6;
            --font-display: 'Fraunces', Georgia, serif;
            --font-body: 'Source Sans 3', 'Segoe UI', sans-serif;
        }
        .login-page {
            min-height: 100dvh;
            font-family: var(--font-body);
            color: var(--login-ink);
            background: #1c1917;
        }
        .login-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(160deg, rgba(16, 14, 12, 0.78) 0%, rgba(28, 25, 23, 0.55) 45%, rgba(42, 69, 12, 0.45) 100%),
                url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
        }
        .login-shell {
            position: relative;
            z-index: 1;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: max(1.25rem, env(safe-area-inset-top)) 1.25rem max(1.5rem, env(safe-area-inset-bottom));
        }
        @media (min-width: 900px) {
            .login-shell {
                justify-content: center;
                padding: 2.5rem;
            }
            .login-grid {
                display: grid;
                grid-template-columns: 1.1fr 0.9fr;
                gap: 3rem;
                align-items: end;
                max-width: 1040px;
                margin: 0 auto;
                width: 100%;
            }
        }
        .login-brand {
            color: #fff;
            margin-bottom: 1.5rem;
            max-width: 28rem;
        }
        .login-brand h1 {
            font-family: var(--font-display);
            font-size: clamp(2.6rem, 9vw, 4.5rem);
            line-height: 0.95;
            letter-spacing: -0.02em;
            margin: 0;
            text-wrap: balance;
        }
        .login-brand p {
            margin: 0.9rem 0 0;
            font-size: 1.05rem;
            line-height: 1.45;
            color: rgba(255, 255, 255, 0.82);
            max-width: 22rem;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(244, 239, 230, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.25rem;
            padding: 1.35rem 1.25rem 1.4rem;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(10px);
        }
        @media (min-width: 900px) {
            .login-card { padding: 1.75rem; justify-self: end; }
        }
        .login-card h2 {
            font-family: var(--font-display);
            font-size: 1.65rem;
            margin: 0;
        }
        .login-card .sub {
            margin: 0.25rem 0 0;
            color: #57534e;
            font-size: 0.95rem;
        }
        .login-field { margin-top: 1rem; }
        .login-field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }
        .login-field input[type="email"],
        .login-field input[type="password"] {
            width: 100%;
            border: 1px solid #d6d3d1;
            border-radius: 0.85rem;
            background: #fff;
            padding: 0.85rem 0.95rem;
            font-size: 1rem;
            font-family: inherit;
            color: var(--login-ink);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }
        .login-field input:focus {
            border-color: var(--login-leaf);
            box-shadow: 0 0 0 3px rgba(63, 98, 18, 0.18);
        }
        .login-remember {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin-top: 1rem;
            font-size: 0.95rem;
            color: #44403c;
        }
        .login-submit {
            display: block;
            width: 100%;
            margin-top: 1.15rem;
            border: 0;
            border-radius: 0.9rem;
            background: linear-gradient(180deg, #4d7a18 0%, var(--login-leaf-deep) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.95rem 1rem;
            cursor: pointer;
            font-family: inherit;
        }
        .login-submit:active { transform: translateY(1px); }
        .login-error {
            margin-top: 0.9rem;
            border-radius: 0.75rem;
            background: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
            padding: 0.7rem 0.85rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="login-page">
<div class="login-bg" aria-hidden="true"></div>
<div class="login-shell">
    <div class="login-grid">
        <div class="login-brand">
            <h1>LÉNA RESTAURANT</h1>
            <p>Système de gestion ERP / POS — commandes, cuisine, stock et caisse.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-card" autocomplete="on">
            @csrf
            <h2>Connexion</h2>
            <p class="sub">Accédez à votre espace restaurant</p>

            @if ($errors->any())
                <div class="login-error">{{ $errors->first() }}</div>
            @endif

            <div class="login-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', 'admin@lena.restaurant') }}" required autofocus inputmode="email" autocomplete="username">
            </div>

            <div class="login-field">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <label class="login-remember">
                <input type="checkbox" name="remember" value="1">
                Se souvenir de moi
            </label>

            <button type="submit" class="login-submit">Se connecter</button>
        </form>
    </div>
</div>
</body>
</html>
