<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sarouty</title>
    <meta name="description" content="@yield('description', 'Trouvez votre bien immobilier au Maroc : appartements, villas, riads, terrains à vendre ou à louer dans toutes les villes marocaines.')">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold:       { DEFAULT: '#C8963E', light: '#E8B86D', dark: '#9B6E22' },
                        terracotta: { DEFAULT: '#C0614A', light: '#D4876E', dark: '#8F3D2D' },
                        sand:       { DEFAULT: '#F5EFE0', light: '#FBF8F2', dark: '#E8D9C0' },
                        forest:     { DEFAULT: '#2D5016', light: '#3D6B20', dark: '#1A2E0D' },
                        ink:        { DEFAULT: '#1A1410', light: '#2D2520', dark: '#0D0A08' },
                    },
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                        body:    ['Outfit', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Leaflet.js (OpenStreetMap) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- Alpine.js (remplace Livewire pour l'interactivité légère) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; color: #1A1410; background: #FBF8F2; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
        .listing-card:hover .listing-img { transform: scale(1.05); }
        .listing-img { transition: transform 0.5s ease; }
        .leaflet-container { border-radius: 12px; }
    </style>

    @stack('styles')
</head>
<body class="antialiased">

    {{-- Spinner de transition global dynamique (Clé et Maison) --}}
    <style>
        .sarouty-loader .key-icon {
            animation: keyUnlock 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
            /* Le point d'origine tourne autour de la boucle de la clé (en haut à droite dans ce svg) */
            transform-origin: 80% 20%; 
        }
        .sarouty-loader .house-icon {
            animation: houseReact 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sarouty-loader .glow-circle {
            animation: glowPulse 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes keyUnlock {
            0% { transform: translate(30px, 15px) rotate(45deg) scale(0); opacity: 0; }
            25% { transform: translate(5px, 0px) rotate(45deg) scale(1); opacity: 1; }
            45% { transform: translate(0px, 0px) rotate(0deg); opacity: 1; }
            55% { transform: translate(0px, 0px) rotate(-90deg); opacity: 1; } /* La clé tourne */
            75% { transform: translate(15px, 5px) rotate(0deg) scale(1.1); opacity: 1; }
            100% { transform: translate(30px, 15px) rotate(45deg) scale(0); opacity: 0; }
        }

        @keyframes houseReact {
            0%, 45% { transform: scale(1); color: #1A1410; } /* Couleur ink */
            55%, 65% { transform: scale(1.15); color: #C8963E; } /* S'illumine en or */
            100% { transform: scale(1); color: #1A1410; }
        }
        
        @keyframes glowPulse {
            0%, 45% { transform: scale(0.5); opacity: 0; }
            55% { transform: scale(1.5); opacity: 0.3; }
            100% { transform: scale(2.5); opacity: 0; }
        }
    </style>

    <div id="page-transition-spinner" class="fixed inset-0 z-[9999] bg-sand flex flex-col items-center justify-center transition-opacity duration-300 hidden opacity-0 sarouty-loader">
        <div class="relative w-32 h-32 flex items-center justify-center mb-6">
            <!-- Effet de lumière quand la porte s'ouvre -->
            <div class="absolute w-20 h-20 bg-gold rounded-full glow-circle"></div>
            
            <!-- Maison -->
            <svg class="w-16 h-16 house-icon relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            
            <!-- Clé -->
            <svg class="w-12 h-12 text-gold absolute key-icon z-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <div class="font-display text-gold font-bold text-2xl tracking-[0.2em] animate-pulse">SAROUTY</div>
    </div>
    
    <script>
        window.addEventListener('beforeunload', function (e) {
            // Ignore for downloads or specific links if needed
            const spinner = document.getElementById('page-transition-spinner');
            spinner.classList.remove('hidden');
            setTimeout(() => {
                spinner.classList.remove('opacity-0');
            }, 10);
        });
        window.addEventListener('pageshow', function (e) {
            // Hide spinner when going back in history
            if (e.persisted) {
                const spinner = document.getElementById('page-transition-spinner');
                spinner.classList.add('opacity-0');
                setTimeout(() => {
                    spinner.classList.add('hidden');
                }, 300);
            }
        });
    </script>

    @include('layouts.navbar')

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-20 right-4 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-20 right-4 z-50 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-sm">
            <p class="text-sm">{{ session('info') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
             class="fixed top-20 right-4 z-50 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg max-w-sm">
            <ul class="text-sm list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    {{-- JS global : toggle favoris sans Livewire --}}
    <script>
    async function toggleFavorite(listingId, btn) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        try {
            const res = await fetch(`/mon-compte/favoris/${listingId}/toggle`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            });
            if (!res.ok) {
                if (res.status === 401) { window.location.href = '/connexion'; return; }
                return;
            }
            const data = await res.json();
            const heart    = btn.querySelector('.heart-icon');
            const countEl  = btn.querySelector('.fav-count');

            if (data.favorited) {
                heart.style.fill   = '#C8963E';
                heart.style.stroke = '#C8963E';
                btn.classList.add('text-gold');
            } else {
                heart.style.fill   = 'none';
                heart.style.stroke = 'currentColor';
                btn.classList.remove('text-gold');
            }
            if (countEl) countEl.textContent = data.count;
        } catch (e) {
            console.error('toggleFavorite error', e);
        }
    }

    // ── Recherche AJAX (remplace Livewire SearchListings) ──────────────────
    async function searchListings(params, container) {
        container.innerHTML = '<div class="col-span-3 text-center py-10 text-gray-400">Chargement...</div>';
        const qs  = new URLSearchParams(params).toString();
        const res = await fetch(`/api/listings/search?${qs}`, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const html = await res.text();
        container.innerHTML = html;
    }
    </script>

    @stack('scripts')
</body>
</html>
