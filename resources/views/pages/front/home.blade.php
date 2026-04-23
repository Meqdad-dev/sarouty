@extends('layouts.app')

@section('title', 'Sarouty – Immobilier au Maroc')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center pt-16 overflow-hidden">

    {{-- Arrière-plan vidéo --}}
    <video id="heroVideo" autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover">
        <source src="{{ asset('herosection/herosection.mp4') }}" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo.
    </video>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var vid = document.getElementById("heroVideo");
            if(vid) vid.playbackRate = 0.6; // Réduit la vitesse à 60%
        });
    </script>

    {{-- Overlay pour assombrir la vidéo et rendre le texte lisible --}}
    <div class="absolute inset-0 bg-black/60 z-0"></div>



    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-gold/20 border border-gold/40 rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 bg-gold rounded-full animate-pulse"></span>
                <span class="text-gold text-sm font-medium">{{ number_format($stats['total']) }} annonces actives</span>
            </div>

            <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6">
                Trouvez votre<br>
                <span class="text-gold italic">bien idéal</span><br>
                au Maroc
            </h1>

            <p class="text-white/60 text-lg max-w-2xl mx-auto mb-12">
                Des milliers d'appartements, villas, riads et terrains à vendre ou à louer dans toutes les villes du Maroc.
            </p>

            {{-- Barre de recherche principale --}}
            <form action="{{ route('listings.index') }}" method="GET" class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl p-2 shadow-2xl">
                    {{-- Tabs type de transaction --}}
                    <div class="flex gap-1 mb-3 px-2 pt-2">
                        @foreach(['vente' => 'Acheter', 'location' => 'Louer', 'neuf' => 'Neuf', 'vacances' => 'Vacances'] as $value => $label)
                            <button type="button"
                                    onclick="document.getElementById('tab-type').value='{{ $value }}'; this.closest('.tab-group').querySelectorAll('button').forEach(b => b.classList.remove('bg-gold','text-white')); this.classList.add('bg-gold','text-white')"
                                    class="tab-btn px-4 py-1.5 rounded-lg text-sm font-medium transition-all {{ $value === 'vente' ? 'bg-gold text-white' : 'text-ink/60 hover:text-ink' }}"
                                    data-tab-group="transaction">
                                {{ $label }}
                            </button>
                        @endforeach
                        <input type="hidden" name="type" id="tab-type" value="vente">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 px-2 pb-2">
                        {{-- Ville --}}
                        <div class="flex-1 relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-ink/40">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <select name="city" class="w-full pl-10 pr-4 py-3 bg-sand/50 rounded-xl text-sm text-ink border-0 focus:ring-2 focus:ring-gold/30 appearance-none">
                                <option value="">Toutes les villes</option>
                                @foreach(\App\Models\Listing::CITIES as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Type de bien --}}
                        <div class="flex-1 relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-ink/40">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <select name="property" class="w-full pl-10 pr-4 py-3 bg-sand/50 rounded-xl text-sm text-ink border-0 focus:ring-2 focus:ring-gold/30 appearance-none">
                                <option value="">Tous les types</option>
                                @foreach(\App\Models\Listing::PROPERTY_TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Budget max --}}
                        <div class="flex-1 relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-ink/40">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input type="number" name="max_price" placeholder="Budget max (MAD)"
                                   class="w-full pl-10 pr-4 py-3 bg-sand/50 rounded-xl text-sm text-ink border-0 focus:ring-2 focus:ring-gold/30 placeholder:text-ink/40">
                        </div>

                        <button type="submit"
                                class="bg-gold hover:bg-gold-dark text-white font-semibold px-8 py-3 rounded-xl transition-colors flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Stats rapides --}}
        <div class="flex flex-wrap justify-center gap-8 mt-16">
            @foreach([
                ['label' => 'Biens à vendre', 'value' => number_format($stats['vente']), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                ['label' => 'En location', 'value' => number_format($stats['location']), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />'],
                ['label' => 'Villes couvertes', 'value' => $stats['cities'], 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />'],
                ['label' => 'Annonces actives', 'value' => number_format($stats['total']), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'],
            ] as $stat)
                <div class="text-center">
                    <div class="flex justify-center mb-2">
                        <svg class="w-8 h-8 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $stat['icon'] !!}
                        </svg>
                    </div>
                    <div class="font-display text-3xl font-bold text-gold">{{ $stat['value'] }}</div>
                    <div class="text-white/50 text-sm">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     ESTIMATION DE BIEN
═══════════════════════════════════════════════════════════════ --}}
<section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #1A1410 0%, #2D1F12 50%, #1A2810 100%);">
    {{-- Background décor --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #C8963E, transparent);"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full opacity-10" style="background: radial-gradient(circle, #C8963E, transparent);"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-5 border border-gold"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Texte gauche --}}
            <div>
                <div class="inline-flex items-center gap-2 bg-gold/20 border border-gold/40 rounded-full px-4 py-1.5 mb-6">
                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-gold text-sm font-medium">Gratuit &amp; instantané</span>
                </div>

                <h2 class="font-display text-4xl sm:text-5xl font-bold text-white leading-tight mb-6">
                    Estimez votre bien<br>
                    <span class="text-gold italic">en 2 minutes</span>
                </h2>

                <p class="text-white/60 text-lg mb-8 leading-relaxed">
                    Obtenez une estimation précise de la valeur de votre appartement, villa ou terrain grâce à notre algorithme basé sur des milliers de transactions réelles au Maroc.
                </p>

                <ul class="space-y-3 mb-10">
                    @foreach([
                        'Basé sur les prix du marché en temps réel',
                        'Analyse par ville, quartier et type de bien',
                        'Résultat immédiat, sans inscription',
                    ] as $point)
                    <li class="flex items-center gap-3 text-white/70">
                        <span class="w-5 h-5 rounded-full bg-gold/20 border border-gold/50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>

                <button id="openEstimationModal"
                        onclick="document.getElementById('estimation-modal').classList.remove('hidden'); document.getElementById('estimation-modal').classList.add('flex');"
                        class="group inline-flex items-center gap-3 bg-gold hover:bg-gold-dark text-white font-bold text-lg px-10 py-4 rounded-2xl transition-all duration-300 shadow-2xl shadow-gold/30 hover:shadow-gold/50 hover:-translate-y-0.5">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Estimer mon bien gratuitement
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>

            {{-- Illustration droite --}}
            <div class="hidden lg:flex flex-col gap-4">
                {{-- Carte simulée --}}
                <div class="bg-white/5 border border-white/10 backdrop-blur-sm rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-white/50 text-sm">Estimation exemple</span>
                        <span class="text-xs bg-green-500/20 text-green-400 border border-green-500/30 rounded-full px-3 py-1">Résultat</span>
                    </div>
                    <div class="text-center py-4">
                        <div class="text-white/40 text-sm mb-2">Appartement • Casablanca • 90 m²</div>
                        <div class="font-display text-4xl font-bold text-gold mb-1">1 080 000 – 1 350 000</div>
                        <div class="text-white/50 text-sm">MAD</div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-white/10">
                        @foreach(['Prix min' => '1 080 000', 'Estimé' => '1 215 000', 'Prix max' => '1 350 000'] as $label => $val)
                        <div class="text-center">
                            <div class="text-white/40 text-xs mb-1">{{ $label }}</div>
                            <div class="text-white font-semibold text-sm">{{ $val }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4">
                    @foreach([
                        ['val' => '50K+', 'lbl' => 'Transactions analysées'],
                        ['val' => '20', 'lbl' => 'Villes couvertes'],
                        ['val' => '98%', 'lbl' => 'Précision moyenne'],
                    ] as $s)
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                        <div class="font-display text-2xl font-bold text-gold">{{ $s['val'] }}</div>
                        <div class="text-white/40 text-xs mt-1">{{ $s['lbl'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     ANNONCES VEDETTES
═══════════════════════════════════════════════════════════════ --}}
@if($featuredListings->isNotEmpty())
<section class="py-20 bg-sand-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div>
                <p class="text-gold text-sm font-medium uppercase tracking-widest mb-2">Sélection exclusive</p>
                <h2 class="font-display text-4xl font-bold text-ink">Coups de cœur</h2>
            </div>
            <a href="{{ route('listings.index') }}" class="text-sm font-medium text-gold hover:underline hidden sm:block">
                Voir toutes les annonces →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredListings as $listing)
                @include('components.listing-card', ['listing' => $listing])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     CATÉGORIES
═══════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-gradient-to-b from-white to-sand-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="h-px bg-gold/50 w-12 sm:w-24"></div>
                <p class="text-gold text-xs sm:text-sm font-bold uppercase tracking-[0.2em]">Explorer</p>
                <div class="h-px bg-gold/50 w-12 sm:w-24"></div>
            </div>
            <h2 class="font-display text-4xl sm:text-5xl font-bold text-ink">Par type de transaction</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['type' => 'vente',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>', 'label' => 'Achat & Vente',    'desc' => 'Devenez propriétaire'],
                ['type' => 'location', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />', 'label' => 'Location',       'desc' => 'Trouvez votre loyer'],
                ['type' => 'neuf',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />', 'label' => 'Immobilier Neuf', 'desc' => 'Programmes récents'],
                ['type' => 'vacances', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />', 'label' => 'Vacances',       'desc' => 'Locations saisonnières'],
            ] as $cat)
                <a href="{{ route('listings.index', ['type' => $cat['type']]) }}"
                   class="group relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:-translate-y-2 hover:shadow-2xl hover:shadow-gold/20 transition-all duration-300 overflow-hidden flex flex-col h-full">
                    
                    {{-- Watermark Icon --}}
                    <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-gold opacity-[0.03] group-hover:scale-110 group-hover:opacity-10 transition-all duration-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $cat['icon'] !!}
                    </svg>

                    <div class="relative z-10 flex flex-col flex-1">
                        <div class="w-14 h-14 rounded-2xl bg-gold/10 text-gold flex items-center justify-center mb-6 group-hover:bg-gold group-hover:text-white transition-colors duration-500 shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $cat['icon'] !!}
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-2xl text-ink mb-2">{{ $cat['label'] }}</h3>
                        <p class="text-ink/60 text-sm font-medium flex items-center gap-2 mt-auto">
                            {{ $cat['desc'] }}
                            <svg class="w-4 h-4 text-gold opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     DESTINATIONS
═══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-sand-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="h-px bg-gold/50 w-12 sm:w-24"></div>
                <p class="text-gold text-xs sm:text-sm font-bold uppercase tracking-[0.2em]">Nos destinations immobilières</p>
                <div class="h-px bg-gold/50 w-12 sm:w-24"></div>
            </div>
            <h2 class="font-display text-4xl sm:text-5xl font-bold text-ink mb-6">
                Trouver des biens immobiliers au Maroc
            </h2>
            <p class="text-ink/60 text-lg max-w-2xl mx-auto">
                Bénéficiez d'un accompagnement dans toutes les régions du royaume
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['city' => 'Casablanca', 'img' => "images/cities/casablanca.jpg", 'fallback' => 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=800&q=80', 'desc' => 'Capitale économique'],
                ['city' => 'Marrakech', 'img' => "images/cities/marrakech.jpg", 'fallback' => 'https://images.unsplash.com/photo-1548013146-72479768bca0?auto=format&fit=crop&w=800&q=80', 'desc' => 'La ville ocre'],
                ['city' => 'Rabat', 'img' => "images/cities/rabat.jpg", 'fallback' => 'https://images.unsplash.com/photo-1594911762140-5e5d165f12df?auto=format&fit=crop&w=800&q=80', 'desc' => 'Capitale administrative'],
                ['city' => 'Tanger', 'img' => "images/cities/tanger.jpg", 'fallback' => 'https://images.unsplash.com/photo-1574426575971-ce49931cc32b?auto=format&fit=crop&w=800&q=80', 'desc' => 'La perle du Nord'],
                ['city' => 'Agadir', 'img' => "images/cities/agadir.jpg", 'fallback' => 'https://images.unsplash.com/photo-1616892523533-5c02dc21cc99?auto=format&fit=crop&w=800&q=80', 'desc' => 'Capitale du Souss'],
                ['city' => 'Fès', 'img' => "images/cities/fes.jpg", 'fallback' => 'https://images.unsplash.com/photo-1555581122-eb168bb1d8fc?auto=format&fit=crop&w=800&q=80', 'desc' => 'Capitale spirituelle'],
            ] as $dest)
                <a href="{{ route('listings.index', ['city' => $dest['city']]) }}" class="group relative h-80 rounded-2xl overflow-hidden block shadow-lg hover:shadow-2xl transition-shadow">
                    <img src="{{ asset($dest['img']) }}" onerror="this.onerror=null; this.src='{{ $dest['fallback'] }}';" alt="{{ $dest['city'] }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-left">
                        <h3 class="text-3xl font-display font-bold text-white mb-1">{{ $dest['city'] }}</h3>
                        <p class="text-white/80 text-sm flex items-center gap-2">
                            <span>{{ $dest['desc'] }}</span>
                            <svg class="w-4 h-4 text-gold opacity-0 -translate-x-4 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('listings.index') }}"
               class="inline-flex items-center gap-2.5 border-2 border-ink text-ink hover:bg-ink hover:text-white font-semibold px-8 py-3.5 rounded-xl transition-all duration-300 group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Autres villes — Voir toutes les annonces
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>



{{-- ═══════════════════════════════════════════════════════════════
     MODAL ESTIMATION (6 étapes)
═══════════════════════════════════════════════════════════════ --}}
<div id="estimation-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-6" style="background:rgba(0,0,0,0.80);backdrop-filter:blur(6px);">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl relative flex flex-col" style="max-height:92vh;">

        {{-- ── Header fixe ── --}}
        <div class="relative px-6 pt-6 pb-5 flex-shrink-0" style="background:linear-gradient(135deg,#1A1410 0%,#2D1F12 100%);border-radius:1.5rem 1.5rem 0 0;">
            <div class="absolute top-0 right-0 w-36 h-36 rounded-full opacity-10" style="background:radial-gradient(circle,#C8963E,transparent);"></div>
            <button onclick="closeEstimation()" class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-gold rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">Estimation de votre bien</h3>
                    <p class="text-white/50 text-xs">Gratuit &amp; sans inscription</p>
                </div>
            </div>
            {{-- Barre de progression --}}
            <div class="flex gap-1 mt-3">
                @for($i=1;$i<=7;$i++)
                <div id="estim-bar-{{$i}}" class="h-1 flex-1 rounded-full transition-all duration-500 {{ $i===1?'bg-gold':'bg-white/20' }}"></div>
                @endfor
            </div>
            <div id="step-label" class="text-white/40 text-xs mt-1.5">Étape 1 sur 7</div>
        </div>

        {{-- ── Corps scrollable ── --}}
        <div class="overflow-y-auto flex-1 px-6 py-6">

            {{-- ─ ÉTAPE 1 : Type de bien ─ --}}
            <div id="estim-step-1" class="estim-step">
                <h4 class="font-bold text-gray-900 text-lg mb-1">Quel type de bien ?</h4>
                <p class="text-gray-500 text-sm mb-5">Sélectionnez le type de votre propriété</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @foreach([
                        ['appartement','Appartement','M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['villa','Villa / Maison','M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'],
                        ['bureau','Bureau / Local','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['terrain','Terrain','M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'],
                        ['riad','Riad','M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['commerce','Commerce','M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                    ] as [$val,$lbl,$ico])
                    <button type="button" onclick="eSelectType('{{$val}}')" data-type="{{$val}}"
                            class="estim-type-btn flex flex-col items-center gap-2 p-4 rounded-2xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 transition-all group">
                        <svg class="w-7 h-7 text-gray-400 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{$ico}}"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-700 group-hover:text-gold transition-colors">{{$lbl}}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- ─ ÉTAPE 2 : Type d'opération ─ --}}
            <div id="estim-step-2" class="estim-step hidden">
                <h4 class="font-bold text-gray-900 text-lg mb-1">Type d'opération</h4>
                <p class="text-gray-500 text-sm mb-5">Vente, location ou autre ?</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['vente','Vente','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','Achat / Investissement'],
                        ['location','Location longue durée','M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z','Appartement, villa...'],
                        ['neuf','Immobilier neuf','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','Programme neuf'],
                        ['vacances','Location vacances','M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z','Court séjour'],
                    ] as [$val,$lbl,$ico,$desc])
                    <button type="button" onclick="eSelectTransaction('{{$val}}')" data-txn="{{$val}}"
                            class="estim-txn-btn flex items-center gap-3 p-4 rounded-2xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 transition-all text-left group">
                        <div class="w-10 h-10 bg-gold/10 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-gold/20 transition-colors">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{$ico}}"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-800 group-hover:text-gold transition-colors">{{$lbl}}</div>
                            <div class="text-xs text-gray-400">{{$desc}}</div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- ─ ÉTAPE 3 : Ville ─ --}}
            <div id="estim-step-3" class="estim-step hidden">
                <h4 class="font-bold text-gray-900 text-lg mb-1">Où se situe votre bien ?</h4>
                <p class="text-gray-500 text-sm mb-5">La ville influence fortement le prix au m²</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @foreach(['Casablanca','Rabat','Marrakech','Tanger','Agadir','Fès','Meknès','Oujda','El Jadida','Tétouan','Essaouira','Ifrane'] as $city)
                    <button type="button" onclick="eSelectCity('{{$city}}')" data-city="{{$city}}"
                            class="estim-city-btn px-3 py-2.5 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-sm font-medium text-gray-700 hover:text-gold transition-all">{{$city}}</button>
                    @endforeach
                </div>
            </div>

            {{-- ─ ÉTAPE 4 : Caractéristiques ─ --}}
            <div id="estim-step-4" class="estim-step hidden">
                <h4 class="font-bold text-gray-900 text-lg mb-1">Caractéristiques du bien</h4>
                <p class="text-gray-500 text-sm mb-5">Affinez pour une estimation plus précise</p>
                <div class="space-y-5">
                    {{-- Surface --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Surface habitable (m²) *</label>
                        <div class="flex items-center gap-3">
                            <input type="range" id="estim-surface-range" min="20" max="1000" step="10" value="100"
                                   oninput="document.getElementById('estim-surface-input').value=this.value;updateSurfaceLabel();"
                                   class="flex-1 h-2 rounded-lg accent-gold cursor-pointer">
                            <input type="number" id="estim-surface-input" min="1" max="99999" value="100"
                                   oninput="document.getElementById('estim-surface-range').value=Math.min(this.value,1000);updateSurfaceLabel();"
                                   class="w-20 border border-gray-200 rounded-xl px-2 py-1.5 text-sm text-center font-bold focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                            <span class="text-gray-500 text-sm font-medium">m²</span>
                        </div>
                        <div id="estim-surface-label" class="text-center text-gold font-semibold text-sm mt-1">100 m²</div>
                    </div>
                    {{-- Grille chambres / SDB / étage --}}
                    <div id="rooms-fields-wrap" class="grid grid-cols-3 gap-3">
                        {{-- Chambres --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Chambres</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach([0,1,2,3,4,'5+'] as $r)
                                <button type="button" onclick="eSelectBedrooms('{{$r}}')" data-bed="{{$r}}"
                                        class="estim-bed-btn w-9 h-9 rounded-lg border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-bold text-gray-600 hover:text-gold transition-all">{{$r}}</button>
                                @endforeach
                            </div>
                        </div>
                        {{-- Salles de bain --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Salles de bain</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach([0,1,2,3,'4+'] as $b)
                                <button type="button" onclick="eSelectBathrooms('{{$b}}')" data-bath="{{$b}}"
                                        class="estim-bath-btn w-9 h-9 rounded-lg border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-bold text-gray-600 hover:text-gold transition-all">{{$b}}</button>
                                @endforeach
                            </div>
                        </div>
                        {{-- Étage --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Étage</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(['RDC',1,2,3,4,'5+'] as $f)
                                <button type="button" onclick="eSelectFloor('{{$f}}')" data-floor="{{$f}}"
                                        class="estim-floor-btn w-9 h-9 rounded-lg border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-bold text-gray-600 hover:text-gold transition-all">{{$f}}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- Année de construction --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Année de construction</label>
                        <div class="flex gap-2 flex-wrap">
                            @foreach([['Avant 1980','<1980'],['1980–2000','1980-2000'],['2000–2010','2000-2010'],['2010–2020','2010-2020'],['Après 2020','>2020']] as [$yearLbl,$yearVal])
                            <button type="button" onclick="eSelectYear('{{$yearVal}}')" data-year="{{$yearVal}}"
                                    class="estim-year-btn px-3 py-1.5 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-semibold text-gray-600 hover:text-gold transition-all">{{$yearLbl}}</button>
                            @endforeach
                        </div>
                    </div>
                    {{-- État du bien --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">État du bien</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([['neuf','Neuf ✨'],['excellent','Excellent'],['bon','Bon état'],['a_renover','À rénover']] as [$condVal,$condLbl])
                            <button type="button" onclick="eSelectCondition('{{$condVal}}')" data-cond="{{$condVal}}"
                                    class="estim-cond-btn py-2 px-2 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-semibold text-gray-700 hover:text-gold transition-all">{{$condLbl}}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <button onclick="estimGoTo(5)" class="mt-6 w-full bg-gold hover:bg-gold-dark text-white font-bold py-3.5 rounded-2xl transition-all shadow-lg shadow-gold/30 text-base flex items-center justify-center gap-2">
                    Continuer <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>

            {{-- ─ ÉTAPE 5 : Équipements / À propos du bien ─ --}}
            <div id="estim-step-5" class="estim-step hidden">
                <h4 class="font-bold text-gray-900 text-lg mb-1">À propos du bien</h4>
                <p class="text-gray-500 text-sm mb-5">Cliquez sur les équipements disponibles</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5" id="amenity-grid">
                    @foreach([
                        ['garage','Garage','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16','ga'],
                        ['garden','Jardin','M12 19l9 2-9-18-9 18 9-2zm0 0v-8','gd'],
                        ['terrace','Terrasse','M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','tc'],
                        ['pool','Piscine','M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z','pl'],
                        ['elevator','Ascenseur','M8 7l4-4m0 0l4 4m-4-4v18','el'],
                        ['parking','Parking','M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4','pk'],
                        ['furnished','Meublé','M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','fu'],
                        ['security','Sécurité','M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','sc'],
                    ] as [$key,$lbl,$ico,$code])
                    <button type="button" onclick="eToggleAmenity('{{$key}}','{{$code}}')" data-amenity="{{$key}}"
                            class="estim-amenity-btn flex flex-col items-center gap-2 p-3 rounded-2xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 transition-all group cursor-pointer">
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{$ico}}"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-600 group-hover:text-gold transition-colors">{{$lbl}}</span>
                    </button>
                    @endforeach
                </div>
                {{-- Sous-champs dynamiques --}}
                <div id="amenity-sub-fields" class="mt-4 space-y-3"></div>

                <button onclick="eComputeAndGo()" class="mt-6 w-full bg-gold hover:bg-gold-dark text-white font-bold py-3.5 rounded-2xl transition-all shadow-lg shadow-gold/30 text-base flex items-center justify-center gap-2">
                    Calculer mon estimation
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </button>
            </div>

            {{-- ─ ÉTAPE 6 : Infos contact ─ --}}
            <div id="estim-step-6" class="estim-step hidden">
                <div class="bg-gray-50 rounded-2xl p-5 space-y-4">
                    <h4 class="font-bold text-gray-900 text-lg mb-1">Vos informations</h4>
                    <p class="text-gray-500 text-sm mb-5">Veuillez renseigner vos coordonnées pour découvrir votre estimation.</p>

                    {{-- Type utilisateur --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Quel type d'utilisateur êtes-vous ? *</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach([['proprietaire','Propriétaire'],['acheteur','Acheteur'],['locataire','Locataire'],['agent','Agent'],['investisseur','Investisseur']] as [$uv,$ul])
                            <button type="button" onclick="eSelectUserType('{{$uv}}')" data-utype="{{$uv}}"
                                    class="estim-utype-btn px-3 py-1.5 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-semibold text-gray-600 hover:text-gold transition-all">{{$ul}}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Aide professionnelle --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Souhaitez-vous une aide professionnelle ?</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="eTogglePro(true)" id="btn-pro-yes"
                                    class="flex-1 py-2 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-semibold text-gray-600 hover:text-gold transition-all">✅ Oui</button>
                            <button type="button" onclick="eTogglePro(false)" id="btn-pro-no"
                                    class="flex-1 py-2 rounded-xl border-2 border-gray-200 hover:border-gray-300 text-xs font-semibold text-gray-600 transition-all">❌ Non</button>
                        </div>
                    </div>

                    {{-- Propriétaire --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Êtes-vous le propriétaire ?</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="eToggleOwner(true)" id="btn-owner-yes"
                                    class="flex-1 py-2 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-semibold text-gray-600 hover:text-gold transition-all">Oui</button>
                            <button type="button" onclick="eToggleOwner(false)" id="btn-owner-no"
                                    class="flex-1 py-2 rounded-xl border-2 border-gray-200 hover:border-gray-300 text-xs font-semibold text-gray-600 transition-all">Non</button>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Quand souhaitez-vous acheter/vendre ?</label>
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach([['maintenant','Immédiatement'],['3mois','Dans 3 mois'],['6mois','Dans 6 mois'],['plus','Plus tard']] as [$tv,$tl])
                            <button type="button" onclick="eSelectTimeline('{{$tv}}')" data-timeline="{{$tv}}"
                                    class="estim-timeline-btn py-2 rounded-xl border-2 border-gray-200 hover:border-gold hover:bg-gold/5 text-xs font-semibold text-gray-600 hover:text-gold transition-all">{{$tl}}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Nom / Email / Téléphone --}}
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <input type="text" id="estim-name" placeholder="Votre nom *" required
                                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none w-full">
                            <span id="err-name" class="text-red-500 text-xs hidden">Veuillez entrer votre nom.</span>
                        </div>
                        <div>
                            <input type="email" id="estim-email" placeholder="Votre email *" required
                                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none w-full">
                            <span id="err-email" class="text-red-500 text-xs hidden">Veuillez entrer une adresse email valide.</span>
                        </div>
                        <div>
                            <input type="tel" id="estim-phone" placeholder="Votre téléphone *" required
                                   class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none w-full">
                            <span id="err-phone" class="text-red-500 text-xs hidden">Veuillez entrer votre numéro de téléphone.</span>
                        </div>
                    </div>

                    <button onclick="eSubmitInfo()" id="btn-submit-info"
                            class="mt-4 w-full bg-gold hover:bg-gold-dark text-white font-bold py-3.5 rounded-2xl transition-all shadow-lg shadow-gold/30 text-base flex items-center justify-center gap-2">
                        Voir mon estimation
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                    <div id="estim-save-error" class="hidden text-center text-red-500 font-semibold text-sm py-2">Une erreur est survenue, veuillez réessayer.</div>
                </div>
            </div>

            {{-- ─ ÉTAPE 7 : Résultats ─ --}}
            <div id="estim-step-7" class="estim-step hidden">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-6 mb-6 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10" style="background:radial-gradient(circle at 70% 30%,#C8963E,transparent);"></div>
                    <div class="relative">
                        <div class="text-white/50 text-sm mb-2">Valeur estimée</div>
                        <div id="estim-price-mid" class="font-display text-4xl font-bold text-gold mb-1">—</div>
                        <div class="text-white/40 text-sm">MAD</div>
                        <div id="estim-result-desc" class="text-white/40 text-sm mt-3"></div>
                        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/10">
                            <div><div class="text-white/40 text-xs mb-1">Estimation basse</div><div id="estim-price-min" class="text-white font-semibold text-sm">—</div></div>
                            <div class="border-x border-white/10"><div class="text-white/40 text-xs mb-1">Prix au m²</div><div id="estim-price-sqm" class="text-gold font-semibold text-sm">—</div></div>
                            <div><div class="text-white/40 text-xs mb-1">Estimation haute</div><div id="estim-price-max" class="text-white font-semibold text-sm">—</div></div>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <p class="text-amber-800 text-sm leading-relaxed">⚠️ <strong>Estimation indicative.</strong> Valeur calculée à partir de données de marché. Consultez un agent pour une évaluation précise.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="resetEstimation()" class="flex-1 py-3 rounded-xl border-2 border-gray-200 hover:border-gray-300 text-gray-700 font-semibold transition-all text-sm">Nouvelle estimation</button>
                    @if(!auth()->check() || !auth()->user()->isClient())
                    <a href="{{ auth()->check() ? route('user.listings.create') : route('register') }}" class="flex-1 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-semibold transition-all text-sm text-center">Publier une annonce</a>
                    @else
                    <a href="{{ route('listings.index') }}" class="flex-1 py-3 rounded-xl bg-gold hover:bg-gold-dark text-white font-semibold transition-all text-sm text-center">Voir les annonces</a>
                    @endif
                </div>
            </div>

        </div>{{-- fin overflow --}}

        {{-- ── Footer navigation ── --}}
        <div id="estim-nav" class="flex items-center px-6 pb-5 pt-2 flex-shrink-0 border-t border-gray-100">
            <button id="estim-prev" onclick="estimPrev()" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </button>
            <div class="flex-1 text-center">
                @if(!auth()->check() || !auth()->user()->isClient())
                <a id="estim-publish-link" href="{{ auth()->check() ? route('user.listings.create') : route('register') }}"
                   class="hidden text-xs text-gold hover:underline font-medium">Publier mon annonce →</a>
                @endif
            </div>
        </div>
    </div>
</div>



{{-- ═══════════════════════════════════════════════════════════════
     ANNONCES RÉCENTES
═══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-sand">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div>
                <p class="text-gold text-sm font-medium uppercase tracking-widest mb-2">Dernières parutions</p>
                <h2 class="font-display text-4xl font-bold text-ink">Annonces récentes</h2>
            </div>
            <a href="{{ route('listings.index') }}" class="text-sm font-medium text-gold hover:underline hidden sm:block">
                Voir toutes →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($recentListings as $listing)
                @include('components.listing-card', ['listing' => $listing])
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('listings.index') }}"
               class="inline-flex items-center gap-2 bg-ink text-white font-medium px-8 py-3 rounded-xl hover:bg-ink/80 transition-colors">
                Voir toutes les annonces
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     ÉTAPES DE PUBLICATION
═══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-gold text-sm font-medium uppercase tracking-widest mb-2">Simple et rapide</p>
            <h2 class="font-display text-4xl font-bold text-ink">4 étapes pour publier votre annonce</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
            {{-- Ligne de connexion (desktop) --}}
            <div class="hidden lg:block absolute top-12 left-[12%] right-[12%] h-[2px] bg-gradient-to-r from-gold/10 via-gold to-gold/10 z-0"></div>

            @foreach([
                ['num' => '1', 'title' => 'Créer un compte', 'desc' => 'Inscrivez-vous gratuitement en quelques clics pour accéder à votre espace personnel.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                ['num' => '2', 'title' => 'Décrire le bien', 'desc' => 'Ajoutez vos plus belles photos, fixez le prix et détaillez les caractéristiques.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                ['num' => '3', 'title' => 'Validation', 'desc' => 'Notre équipe vérifie et valide votre annonce pour garantir la qualité de la plateforme.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                ['num' => '4', 'title' => 'En ligne !', 'desc' => 'Votre bien est visible par des milliers d\'acheteurs et locataires potentiels.', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
            ] as $step)
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-24 h-24 rounded-full bg-white border-4 border-sand shadow-xl flex items-center justify-center mb-6 relative group-hover:border-gold transition-colors duration-500">
                        <svg class="w-10 h-10 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $step['icon'] !!}
                        </svg>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-ink text-white font-bold flex items-center justify-center text-sm shadow-lg group-hover:bg-gold transition-colors duration-500">
                            {{ $step['num'] }}
                        </div>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink mb-3">{{ $step['title'] }}</h3>
                    <p class="text-ink/60 text-sm leading-relaxed px-2">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
        
        <div class="mt-16 text-center">
            @if(!auth()->check() || !auth()->user()->isClient())
            <a href="{{ auth()->check() ? route('user.listings.create') : route('register') }}"
               class="inline-flex items-center gap-2 bg-ink hover:bg-ink/80 text-white font-semibold px-8 py-3.5 rounded-xl transition-all shadow-lg hover:-translate-y-1">
                Commencer maintenant
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     CTA PUBLIER
═══════════════════════════════════════════════════════════════ --}}
<section class="py-20"
         style="background: linear-gradient(135deg, #C8963E 0%, #9B6E22 100%);">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="font-display text-4xl sm:text-5xl font-bold text-white mb-4">
            Vous avez un bien à vendre ou à louer ?
        </h2>
        <p class="text-white/80 text-lg mb-8">
            Publiez votre annonce gratuitement et touchez des milliers d'acheteurs et locataires potentiels.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if(!auth()->check() || !auth()->user()->isClient())
            <a href="{{ auth()->check() ? route('user.listings.create') : route('register') }}"
               class="bg-white text-gold font-semibold px-8 py-4 rounded-xl hover:bg-sand transition-colors text-lg">
                Publier une annonce
            </a>
            @endif
            <a href="{{ route('listings.index') }}"
               class="border-2 border-white text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition-colors text-lg">
                Parcourir les annonces
            </a>
        </div>
    </div>
</section>

<script>
// ═══════════════════════════════════════════════════════════
// DONNÉES MARCHÉ MAROC (prix au m² en MAD)
// ═══════════════════════════════════════════════════════════
const MARKET = {
    appartement: {
        Casablanca:[10000,13500,18000], Rabat:[9000,12000,16000],
        Marrakech:[8500,11500,16500],  Tanger:[7500,10000,14000],
        Agadir:[7000,9500,13000],      'Fès':[5500,7500,10500],
        'Meknès':[5000,6800,9500],     Oujda:[4500,6000,8500],
        'El Jadida':[5500,7500,10000], 'Tétouan':[6000,8000,11000],
        Essaouira:[7000,9500,13000],   Ifrane:[8000,11000,15000],
        default:[6000,8500,12000],
    },
    villa:{
        Casablanca:[8000,12000,18000], Rabat:[7500,11000,16000],
        Marrakech:[7000,11500,18000],  Tanger:[6500,9500,14000],
        Agadir:[6000,9000,13000],      'Fès':[4500,6500,9500],
        'Meknès':[4000,6000,9000],     Oujda:[3500,5500,8000],
        'El Jadida':[5000,7000,10000], 'Tétouan':[5500,8000,12000],
        Essaouira:[6000,9000,13000],   Ifrane:[7000,11000,16000],
        default:[5000,8000,12000],
    },
    terrain:{
        Casablanca:[3000,6000,12000],  Rabat:[2500,5000,10000],
        Marrakech:[2000,4500,9000],    Tanger:[2000,4000,8000],
        Agadir:[1500,3500,7000],       'Fès':[800,2000,4500],
        'Meknès':[700,1800,4000],      Oujda:[600,1500,3500],
        'El Jadida':[1000,2500,5000],  'Tétouan':[1200,2800,5500],
        Essaouira:[1500,3500,7000],    Ifrane:[2000,4500,9000],
        default:[1000,2500,5500],
    },
    riad:     { Marrakech:[7000,13000,22000], 'Fès':[5000,10000,18000], default:[5000,9000,15000] },
    bureau:   { Casablanca:[7000,11000,16000], Rabat:[6000,9000,13000], default:[4500,7000,11000] },
    commerce: { Casablanca:[8000,14000,22000], Marrakech:[7000,12000,20000], default:[5000,9000,15000] },
};
const COND_MULT = { neuf:1.18, excellent:1.08, bon:1.0, a_renover:0.82 };
const BED_MULT  = { '0':1.12,'1':1.08,'2':1.04,'3':1.0,'4':0.97,'5+':0.93 };
const YEAR_MULT = { '<1980':0.85,'1980-2000':0.92,'2000-2010':0.98,'2010-2020':1.04,'>2020':1.12 };
// Bonus équipements
const AMENITY_BONUS = { garage:0.03, garden:0.02, terrace:0.02, pool:0.05, elevator:0.02, parking:0.02, furnished:0.04, security:0.01 };

// ═══════════════════════════════════════════════════════════
// ÉTAT
// ═══════════════════════════════════════════════════════════
let eStep = 1;
let eD = {
    type:null, transaction:null, city:null,
    surface:100, bedrooms:null, bathrooms:null, floor:null, year:null, condition:null,
    amenities: {},           // { garage:true, garage_places:2, garden:true, garden_surface:50, ... }
    userType:null, wantsPro:null, isOwner:null, timeline:null,
};
let eResult = { min:0, mid:0, max:0, sqm:0 };

function fmt(n){ return Math.round(n).toLocaleString('fr-MA').replace(/,/g,' '); }

// ═══════════════════════════════════════════════════════════
// NAVIGATION
// ═══════════════════════════════════════════════════════════
function estimGoTo(n) {
    document.querySelectorAll('.estim-step').forEach(el => el.classList.add('hidden'));
    const target = document.getElementById('estim-step-' + n);
    if (!target) return;
    target.classList.remove('hidden');
    // Progress bars
    for(let i=1;i<=7;i++){
        const b = document.getElementById('estim-bar-'+i);
        if(b){ b.style.backgroundColor = i<=n ? '#C8963E' : 'rgba(255,255,255,0.2)'; }
    }
    document.getElementById('step-label').textContent = 'Étape '+n+' sur 7';
    // Prev button
    const prev = document.getElementById('estim-prev');
    if(prev) prev.style.visibility = n>1 ? 'visible' : 'hidden';
    // Publish link step 7
    const pl = document.getElementById('estim-publish-link');
    if(pl) { n===7 ? pl.classList.remove('hidden') : pl.classList.add('hidden'); }
    eStep = n;
}
function estimPrev(){ if(eStep>1) estimGoTo(eStep-1); }
function closeEstimation(){
    document.getElementById('estimation-modal').classList.add('hidden');
    document.getElementById('estimation-modal').classList.remove('flex');
}

// ═══════════════════════════════════════════════════════════
// SÉLECTEURS (step 1–5)
// ═══════════════════════════════════════════════════════════
function activateBtn(selector, attr, val){
    document.querySelectorAll(selector).forEach(b => {
        const on = b.dataset[attr] === String(val);
        b.classList.toggle('border-gold', on);
        b.classList.toggle('bg-gold/10', on);
        b.classList.toggle('text-gold', on);
        b.classList.toggle('border-gray-200', !on);
    });
}

// Step 1 – type de bien
function eSelectType(v){
    eD.type = v;
    activateBtn('.estim-type-btn','type',v);
    // Masquer rooms-fields-wrap pour terrain
    const wrap = document.getElementById('rooms-fields-wrap');
    if(wrap) wrap.style.display = v==='terrain' ? 'none' : '';
    setTimeout(() => estimGoTo(2), 280);
}

// Step 2 – type d'opération
function eSelectTransaction(v){
    eD.transaction = v;
    activateBtn('.estim-txn-btn','txn',v);
    setTimeout(() => estimGoTo(3), 280);
}

// Step 3 – ville
function eSelectCity(v){
    eD.city = v;
    activateBtn('.estim-city-btn','city',v);
    setTimeout(() => estimGoTo(4), 280);
}

// Step 4 – caractéristiques
function eSelectBedrooms(v){ eD.bedrooms=v; activateBtn('.estim-bed-btn','bed',v); }
function eSelectBathrooms(v){ eD.bathrooms=v; activateBtn('.estim-bath-btn','bath',v); }
function eSelectFloor(v){ eD.floor=v; activateBtn('.estim-floor-btn','floor',v); }
function eSelectYear(v){ eD.year=v; activateBtn('.estim-year-btn','year',v); }
function eSelectCondition(v){ eD.condition=v; activateBtn('.estim-cond-btn','cond',v); }
function updateSurfaceLabel(){
    const v = document.getElementById('estim-surface-input').value;
    const l = document.getElementById('estim-surface-label');
    if(l) l.textContent = v+' m²';
    eD.surface = parseFloat(v)||100;
}

// Step 5 – équipements avec sous-champs dynamiques
function eToggleAmenity(key, code){
    const btn = document.querySelector('[data-amenity="'+key+'"]');
    if(!btn) return;
    const isOn = !eD.amenities[key];
    eD.amenities[key] = isOn;
    btn.classList.toggle('border-gold', isOn);
    btn.classList.toggle('bg-gold/10', isOn);
    btn.classList.toggle('text-gold', isOn);
    btn.classList.toggle('border-gray-200', !isOn);
    renderAmenitySubFields();
}
function renderAmenitySubFields(){
    const container = document.getElementById('amenity-sub-fields');
    if(!container) return;
    container.innerHTML = '';
    // Garage → nombre de places
    if(eD.amenities['garage']){
        container.innerHTML += `
        <div class="flex items-center gap-3 p-3 bg-gold/5 border border-gold/30 rounded-xl">
            <svg class="w-5 h-5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
            <label class="text-sm font-medium text-gray-700 flex-1">Nombre de places garage</label>
            <input type="number" min="1" max="20" value="${eD.amenities.garage_places||1}"
                   oninput="eD.amenities.garage_places=parseInt(this.value)||1"
                   class="w-20 border border-gold/30 bg-white rounded-xl px-2 py-1.5 text-sm text-center font-bold focus:ring-2 focus:ring-gold/30 outline-none">
        </div>`;
    }
    // Jardin → surface
    if(eD.amenities['garden']){
        container.innerHTML += `
        <div class="flex items-center gap-3 p-3 bg-gold/5 border border-gold/30 rounded-xl">
            <svg class="w-5 h-5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <label class="text-sm font-medium text-gray-700 flex-1">Surface du jardin (m²)</label>
            <input type="number" min="1" max="99999" value="${eD.amenities.garden_surface||60}"
                   oninput="eD.amenities.garden_surface=parseFloat(this.value)||60"
                   class="w-20 border border-gold/30 bg-white rounded-xl px-2 py-1.5 text-sm text-center font-bold focus:ring-2 focus:ring-gold/30 outline-none">
        </div>`;
    }
    // Terrasse → surface
    if(eD.amenities['terrace']){
        container.innerHTML += `
        <div class="flex items-center gap-3 p-3 bg-gold/5 border border-gold/30 rounded-xl">
            <svg class="w-5 h-5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <label class="text-sm font-medium text-gray-700 flex-1">Surface de la terrasse (m²)</label>
            <input type="number" min="1" max="9999" value="${eD.amenities.terrace_surface||20}"
                   oninput="eD.amenities.terrace_surface=parseFloat(this.value)||20"
                   class="w-20 border border-gold/30 bg-white rounded-xl px-2 py-1.5 text-sm text-center font-bold focus:ring-2 focus:ring-gold/30 outline-none">
        </div>`;
    }
}

// Step 6 – profil utilisateur
function eSelectUserType(v){ eD.userType=v; activateBtn('.estim-utype-btn','utype',v); }
function eSelectTimeline(v){ eD.timeline=v; activateBtn('.estim-timeline-btn','timeline',v); }
function eTogglePro(v){
    eD.wantsPro=v;
    document.getElementById('btn-pro-yes').classList.toggle('border-gold',v);
    document.getElementById('btn-pro-yes').classList.toggle('bg-gold/10',v);
    document.getElementById('btn-pro-yes').classList.toggle('text-gold',v);
    document.getElementById('btn-pro-no').classList.toggle('border-gold',!v);
    document.getElementById('btn-pro-no').classList.toggle('bg-gold/10',!v);
    document.getElementById('btn-pro-no').classList.toggle('text-gold',!v);
}
function eToggleOwner(v){
    eD.isOwner=v;
    document.getElementById('btn-owner-yes').classList.toggle('border-gold',v);
    document.getElementById('btn-owner-yes').classList.toggle('bg-gold/10',v);
    document.getElementById('btn-owner-yes').classList.toggle('text-gold',v);
    document.getElementById('btn-owner-no').classList.toggle('border-gold',!v);
    document.getElementById('btn-owner-no').classList.toggle('bg-gold/10',!v);
    document.getElementById('btn-owner-no').classList.toggle('text-gold',!v);
}

// ═══════════════════════════════════════════════════════════
// CALCUL
// ═══════════════════════════════════════════════════════════
function eComputeAndGo(){
    const surface = parseFloat(document.getElementById('estim-surface-input').value) || 100;
    eD.surface = surface;

    const type  = eD.type || 'appartement';
    const city  = eD.city || 'default';
    const cond  = eD.condition || 'bon';
    const beds  = eD.bedrooms;
    const yr    = eD.year;

    const typeData = MARKET[type] || MARKET.appartement;
    const cityData = typeData[city] || typeData.default || [6000,9000,13000];
    let [sMin, sMid, sMax] = [...cityData];

    // Condition
    const cM = COND_MULT[cond] || 1;
    sMin*=cM; sMid*=cM; sMax*=cM;
    // Chambres
    if(beds && BED_MULT[String(beds)]){ const bM=BED_MULT[String(beds)]; sMin*=bM; sMid*=bM; sMax*=bM; }
    // Année
    if(yr && YEAR_MULT[yr]){ const yM=YEAR_MULT[yr]; sMin*=yM; sMid*=yM; sMax*=yM; }
    // Équipements
    let bonusTotal = 0;
    Object.keys(eD.amenities).forEach(k => {
        if(eD.amenities[k] && AMENITY_BONUS[k]) bonusTotal += AMENITY_BONUS[k];
    });
    sMin*=(1+bonusTotal); sMid*=(1+bonusTotal); sMax*=(1+bonusTotal);

    eResult = {
        min: Math.round(sMin*surface),
        mid: Math.round(sMid*surface),
        max: Math.round(sMax*surface),
        sqm: Math.round(sMid)
    };

    // Affichage
    const typeLabels = { appartement:'Appartement', villa:'Villa/Maison', bureau:'Bureau/Local', terrain:'Terrain', riad:'Riad', commerce:'Commerce' };
    document.getElementById('estim-result-desc').textContent =
        (typeLabels[type]||type) + ' • ' + (eD.city||'Maroc') + ' • ' + surface+' m²';

    animateVal('estim-price-mid', 0, eResult.mid, 900, v=>fmt(v)+' MAD');
    document.getElementById('estim-price-min').textContent = fmt(eResult.min)+' MAD';
    document.getElementById('estim-price-max').textContent = fmt(eResult.max)+' MAD';
    document.getElementById('estim-price-sqm').textContent = fmt(eResult.sqm)+' MAD/m²';

    estimGoTo(6);
}
function animateVal(id,from,to,dur,fmtFn){
    const el=document.getElementById(id); if(!el) return;
    const t0=performance.now();
    (function step(now){
        const t=Math.min((now-t0)/dur,1);
        const e=t<.5?2*t*t:-1+(4-2*t)*t;
        el.textContent=fmtFn(from+e*(to-from));
        if(t<1) requestAnimationFrame(step);
    })(t0);
}

// ═══════════════════════════════════════════════════════════
// SOUMISSION INFO & ENREGISTREMENT BDD
// ═══════════════════════════════════════════════════════════
async function eSubmitInfo() {
    const nameInput = document.getElementById('estim-name');
    const emailInput = document.getElementById('estim-email');
    const phoneInput = document.getElementById('estim-phone');

    document.getElementById('err-name').classList.add('hidden');
    document.getElementById('err-email').classList.add('hidden');
    document.getElementById('err-phone').classList.add('hidden');
    document.getElementById('estim-save-error').classList.add('hidden');

    let isValid = true;
    if(!nameInput.value.trim()){ document.getElementById('err-name').classList.remove('hidden'); isValid=false; }
    if(!emailInput.value.trim() || !emailInput.value.includes('@')){ document.getElementById('err-email').classList.remove('hidden'); isValid=false; }
    if(!phoneInput.value.trim()){ document.getElementById('err-phone').classList.remove('hidden'); isValid=false; }

    if(!isValid) return;

    if(!eD.userType){
        alert("Veuillez sélectionner votre type d'utilisateur.");
        return;
    }

    const btn = document.getElementById('btn-submit-info');
    if(!btn) return;
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Enregistrement...';

    const payload = {
        property_type:          eD.type,
        transaction_type:       eD.transaction,
        city:                   eD.city,
        surface:                eD.surface,
        bedrooms:               eD.bedrooms ? (eD.bedrooms==='5+'?5:parseInt(eD.bedrooms)) : null,
        bathrooms:              eD.bathrooms ? (eD.bathrooms==='4+'?4:parseInt(eD.bathrooms)) : null,
        floor:                  eD.floor ? (eD.floor==='RDC'?0:(eD.floor==='5+'?5:parseInt(eD.floor))) : null,
        condition:              eD.condition,
        construction_year:      null,
        has_garage:             !!eD.amenities.garage,
        garage_places:          eD.amenities.garage_places||null,
        has_garden:             !!eD.amenities.garden,
        garden_surface:         eD.amenities.garden_surface||null,
        has_terrace:            !!eD.amenities.terrace,
        terrace_surface:        eD.amenities.terrace_surface||null,
        has_pool:               !!eD.amenities.pool,
        has_elevator:           !!eD.amenities.elevator,
        has_parking:            !!eD.amenities.parking,
        is_furnished:           !!eD.amenities.furnished,
        has_security:           !!eD.amenities.security,
        estimated_min:          eResult.min,
        estimated_mid:          eResult.mid,
        estimated_max:          eResult.max,
        price_per_sqm:          eResult.sqm,
        user_type:              eD.userType,
        wants_professional_help:eD.wantsPro===true,
        is_owner:               eD.isOwner===true,
        timeline:               eD.timeline,
        contact_name:           nameInput.value,
        contact_email:          emailInput.value,
        contact_phone:          phoneInput.value,
        _token:                 document.querySelector('meta[name="csrf-token"]')?.content,
    };

    try {
        const res = await fetch('/estimation', {
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':payload._token},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.success){
            // Passer à l'étape 7 (Résultat)
            estimGoTo(7);
        } else {
            document.getElementById('estim-save-error').classList.remove('hidden');
            btn.disabled=false;
            btn.innerHTML='Voir mon estimation';
        }
    } catch(err){
        document.getElementById('estim-save-error').classList.remove('hidden');
        btn.disabled=false;
        btn.innerHTML='Voir mon estimation';
    }
}

// ═══════════════════════════════════════════════════════════
// RESET
// ═══════════════════════════════════════════════════════════
function resetEstimation(){
    eD = { type:null,transaction:null,city:null,surface:100,bedrooms:null,bathrooms:null,floor:null,year:null,condition:null,amenities:{},userType:null,wantsPro:null,isOwner:null,timeline:null };
    eResult = {min:0,mid:0,max:0,sqm:0};
    document.querySelectorAll('.estim-type-btn,.estim-txn-btn,.estim-city-btn,.estim-bed-btn,.estim-bath-btn,.estim-floor-btn,.estim-year-btn,.estim-cond-btn,.estim-amenity-btn,.estim-utype-btn,.estim-timeline-btn').forEach(b=>{
        b.classList.remove('border-gold','bg-gold/10','text-gold');
        b.classList.add('border-gray-200');
    });
    document.getElementById('estim-surface-range').value=100;
    document.getElementById('estim-surface-input').value=100;
    updateSurfaceLabel();
    const sub=document.getElementById('amenity-sub-fields');
    if(sub) sub.innerHTML='';
    const err=document.getElementById('estim-save-error');
    if(err) err.classList.add('hidden');
    const btn=document.getElementById('btn-submit-info');
    if(btn){ btn.disabled=false; btn.innerHTML='Voir mon estimation <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>'; }
    ['estim-name','estim-email','estim-phone'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
    ['err-name','err-email','err-phone'].forEach(id=>{ const el=document.getElementById(id); if(el) el.classList.add('hidden'); });
    estimGoTo(1);
}

// ═══════════════════════════════════════════════════════════
// OVERLAY & ESCAPE
// ═══════════════════════════════════════════════════════════
document.getElementById('estimation-modal').addEventListener('click',function(e){ if(e.target===this) closeEstimation(); });
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeEstimation(); });

// Init prev button visibility
document.getElementById('estim-prev').style.visibility='hidden';
</script>

@endsection
