<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié – Sarouty</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { gold: { DEFAULT: '#C8963E', dark: '#9B6E22' }, sand: { DEFAULT: '#F5EFE0', light: '#FBF8F2' }, ink: { DEFAULT: '#1A1410' } },
                fontFamily: { display: ['"Cormorant Garamond"', 'serif'], body: ['Outfit', 'sans-serif'] }
            }}
        }
    </script>
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="min-h-screen bg-sand-light flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <a href="{{ route('home') }}" class="flex items-center gap-2 justify-center mb-3">
            <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-20 w-auto">
        </a>

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-sand/60">
            <h1 class="font-display text-3xl font-bold text-ink mb-2">Mot de passe oublié</h1>
            <p class="text-sm text-ink/50 mb-6">
                Saisissez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-6">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-ink mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           placeholder="votre@email.ma"
                           class="w-full px-4 py-3 bg-sand rounded-xl border-0 focus:ring-2 focus:ring-gold/30 text-ink text-sm">
                </div>
                <button type="submit"
                        class="w-full bg-gold hover:bg-gold-dark text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            <p class="text-center text-sm text-ink/50 mt-6">
                <a href="{{ route('login') }}" class="text-gold hover:underline">← Retour à la connexion</a>
            </p>
        </div>
    </div>
</body>
</html>
