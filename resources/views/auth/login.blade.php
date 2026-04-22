<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – Sarouty</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold:  { DEFAULT: '#C8963E', light: '#E8B86D', dark: '#9B6E22' },
                        sand:  { DEFAULT: '#F5EFE0', light: '#FBF8F2' },
                        ink:   { DEFAULT: '#1A1410' },
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

    {{-- Panneau gauche : illustration --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
         style="background: linear-gradient(135deg, #1A1410 0%, #2D2520 60%, #C8963E 100%);">

        {{-- Motif géométrique zellige --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="zellige-login" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                        <polygon points="40,0 80,20 80,60 40,80 0,60 0,20" fill="none" stroke="#C8963E" stroke-width="0.5"/>
                        <polygon points="40,15 65,27.5 65,52.5 40,65 15,52.5 15,27.5" fill="none" stroke="#C8963E" stroke-width="0.3"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#zellige-login)"/>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-20 w-auto">
            </a>

            {{-- Citation --}}
            <div>
                <blockquote class="font-display text-4xl italic text-white/90 leading-relaxed mb-6">
                    "Votre maison idéale<br>vous attend au Maroc"
                </blockquote>
                <div class="flex flex-wrap gap-3 mb-8">
                    <span class="bg-white/10 text-white/70 text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Vente
                    </span>
                    <span class="bg-white/10 text-white/70 text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Location
                    </span>
                    <span class="bg-white/10 text-white/70 text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Vacances
                    </span>
                    <span class="bg-white/10 text-white/70 text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Neuf
                    </span>
                </div>
                <div class="flex gap-6 text-white/60 text-sm">
                    <div><span class="font-semibold text-gold text-2xl block font-display">5000+</span>Annonces</div>
                    <div><span class="font-semibold text-gold text-2xl block font-display">20+</span>Villes</div>
                    <div><span class="font-semibold text-gold text-2xl block font-display">98%</span>Satisfaction</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panneau droit : formulaire --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-sand-light">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 mb-3 lg:hidden">
                <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-20 w-auto">
            </a>

            <h1 class="font-display text-4xl font-bold text-ink mb-2">Bon retour !</h1>
            <p class="text-ink/50 mb-8">Connectez-vous à votre espace personnel.</p>

            {{-- Session errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-ink mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           autocomplete="email" required autofocus
                           placeholder="votre@email.ma"
                           class="w-full px-4 py-3 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm placeholder:text-ink/30 transition-all @error('email') border-red-400 @enderror">
                </div>

                {{-- Mot de passe --}}
                <div x-data="{ show: false }">
                    <div class="flex justify-between items-baseline mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-ink">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-gold hover:underline">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" :type="show ? 'text' : 'password'" name="password"
                               autocomplete="current-password" required
                               placeholder="••••••••"
                               class="w-full px-4 py-3 pr-12 bg-white rounded-xl border border-sand/80 focus:border-gold focus:ring-2 focus:ring-gold/20 text-ink text-sm placeholder:text-ink/30 transition-all @error('password') border-red-400 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-ink/30 hover:text-ink/60 transition-colors">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Se souvenir de moi --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-sand text-gold focus:ring-gold">
                    <span class="text-sm text-ink/70">Se souvenir de moi</span>
                </label>

                {{-- Bouton connexion --}}
                <button type="submit"
                        class="w-full bg-gold hover:bg-gold-dark text-white font-semibold py-3.5 rounded-xl transition-colors text-sm tracking-wide">
                    Se connecter
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-sand"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-sand-light px-4 text-xs text-ink/40">ou</span>
                </div>
            </div>

            {{-- Inscription --}}
            @if (Route::has('register'))
                <p class="text-center text-sm text-ink/60">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="text-gold hover:underline font-semibold">
                        Créer un compte gratuit
                    </a>
                </p>
            @endif

            {{-- Retour accueil --}}
            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="text-xs text-ink/40 hover:text-gold transition-colors">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>
