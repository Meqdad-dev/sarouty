<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription – Sarouty</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: { DEFAULT: '#C8963E', dark: '#9B6E22' },
                        sand: { DEFAULT: '#F5EFE0', light: '#FBF8F2' },
                        ink:  { DEFAULT: '#1A1410' },
                    },
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'serif'],
                        body:    ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="min-h-screen bg-sand-light flex">

    {{-- Panneau gauche --}}
    <div class="hidden lg:flex lg:w-2/5 relative overflow-hidden"
         style="background: linear-gradient(160deg, #2D5016 0%, #1A2E0D 50%, #C8963E 100%);">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="z" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse">
                        <polygon points="30,0 60,15 60,45 30,60 0,45 0,15" fill="none" stroke="#E8B86D" stroke-width="0.6"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#z)"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col justify-between p-10 w-full">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-20 w-auto">
            </a>

            <div>
                <h2 class="font-display text-4xl font-bold text-white mb-4 leading-tight">
                    Rejoignez la communauté<br>immobilière du Maroc
                </h2>
                <ul class="space-y-4 text-white/70 text-sm">
                    @foreach([
                        ['M5 13l4 4L19 7', 'Publiez vos annonces gratuitement'],
                        ['M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'Accédez à des milliers de biens'],
                        ['M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'Sauvegardez vos coups de cœur'],
                        ['M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z', 'Contactez directement les propriétaires'],
                        ['M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'Localisez les biens sur la carte'],
                    ] as [$path, $text])
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                            </span>
                            <span>{{ $text }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Panneau droit : formulaire --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-sand-light overflow-y-auto">
        <div class="w-full max-w-md py-8">

            <a href="{{ route('home') }}" class="flex items-center gap-2 mb-3 lg:hidden">
                <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-20 w-auto">
            </a>

            <h1 class="font-display text-4xl font-bold text-ink mb-2">Créer un compte</h1>
            <p class="text-ink/50 mb-8">Rejoignez des milliers d'utilisateurs au Maroc.</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Nom --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-ink mb-1.5">Nom complet *</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           autocomplete="name" required placeholder="Ahmed El Fassi"
                           class="w-full px-4 py-3 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm transition-all @error('name') border-red-400 @enderror">
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-ink mb-1.5">Email *</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           autocomplete="email" required placeholder="votre@email.ma"
                           class="w-full px-4 py-3 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm transition-all @error('email') border-red-400 @enderror">
                </div>

                {{-- Téléphone --}}
                <div>
                    <label for="phone" class="block text-sm font-semibold text-ink mb-1.5">Téléphone</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="+212 6XX XXX XXX"
                           class="w-full px-4 py-3 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm transition-all">
                </div>

                {{-- Rôle --}}
                <div>
                    <label class="block text-sm font-semibold text-ink mb-2">Je suis… *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        @foreach([
                            'client' => ['M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'Client', 'Recherche de biens'],
                            'particulier' => ['M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Particulier', 'Acheteur / vendeur privé'],
                            'agent' => ['M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'Agent immobilier', 'Professionnel de l\'immobilier']
                        ] as $val => [$iconPath, $label, $desc])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="role" value="{{ $val }}"
                                       {{ old('role', 'client') === $val ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="border-2 border-sand peer-checked:border-gold peer-checked:bg-gold/5 rounded-xl p-3 transition-all hover:border-gold/40 h-full">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mb-2 peer-checked:bg-gold/10">
                                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/></svg>
                                    </div>
                                    <div class="text-sm font-semibold text-ink leading-tight">{{ $label }}</div>
                                    <div class="text-[10px] text-ink/40 mt-1 uppercase tracking-wider">{{ $desc }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-ink mb-1.5">Mot de passe *</label>
                    <div class="relative">
                        <input id="password" :type="show ? 'text' : 'password'" name="password"
                               autocomplete="new-password" required placeholder="Minimum 8 caractères"
                               class="w-full px-4 py-3 pr-12 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm transition-all @error('password') border-red-400 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-ink/30 hover:text-ink/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Confirmation mot de passe --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-ink mb-1.5">Confirmer le mot de passe *</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           autocomplete="new-password" required placeholder="••••••••"
                           class="w-full px-4 py-3 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm transition-all">
                </div>

                {{-- CGU --}}
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="terms" required
                           class="w-4 h-4 rounded border-sand text-gold focus:ring-gold mt-0.5 flex-shrink-0">
                    <span class="text-xs text-ink/60">
                        J'accepte les <a href="#" class="text-gold hover:underline">Conditions d'utilisation</a>
                        et la <a href="#" class="text-gold hover:underline">Politique de confidentialité</a> de Sarouty.
                    </span>
                </label>

                <button type="submit"
                        class="w-full bg-gold hover:bg-gold-dark text-white font-semibold py-3.5 rounded-xl transition-colors text-sm tracking-wide mt-2">
                    Créer mon compte gratuitement
                </button>
            </form>

            <p class="text-center text-sm text-ink/60 mt-6">
                Déjà inscrit ?
                <a href="{{ route('login') }}" class="text-gold hover:underline font-semibold">Se connecter</a>
            </p>
            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-xs text-ink/40 hover:text-gold transition-colors">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>
