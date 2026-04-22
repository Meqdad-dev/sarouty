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
