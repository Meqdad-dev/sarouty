@extends('layouts.app')

@section('title', $listing->title . ' – Sarouty')
@section('description', Str::limit($listing->description, 160))

@section('content')
<div class="pt-20 min-h-screen bg-sand-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Fil d'Ariane --}}
        <nav class="flex items-center gap-2 text-xs text-ink/50 mb-6">
            <a href="{{ route('home') }}" class="hover:text-gold">Accueil</a>
            <span>/</span>
            <a href="{{ route('listings.index') }}" class="hover:text-gold">Annonces</a>
            <span>/</span>
            <a href="{{ route('listings.index', ['city' => $listing->city]) }}" class="hover:text-gold">{{ $listing->city }}</a>
            <span>/</span>
            <span class="text-ink line-clamp-1">{{ Str::limit($listing->title, 50) }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ── Colonne principale (2/3) ── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Galerie d'images --}}
                <div x-data="{ active: 0 }" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-sand/60">
                    @if($listing->images->isNotEmpty())
                        {{-- Image principale --}}
                        <div class="relative h-72 sm:h-96 overflow-hidden">
                            @foreach($listing->images as $index => $image)
                                <img src="{{ $image->url }}"
                                     alt="{{ $listing->title }} - photo {{ $index + 1 }}"
                                     x-show="active === {{ $index }}"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="w-full h-full object-cover absolute inset-0">
                            @endforeach

                            {{-- Navigation galerie --}}
                            @if($listing->images->count() > 1)
                                <button @click="active = (active - 1 + {{ $listing->images->count() }}) % {{ $listing->images->count() }}"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all">
                                    ‹
                                </button>
                                <button @click="active = (active + 1) % {{ $listing->images->count() }}"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all">
                                    ›
                                </button>
                                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                                    @foreach($listing->images as $index => $image)
                                        <button @click="active = {{ $index }}"
                                                :class="active === {{ $index }} ? 'bg-white scale-110' : 'bg-white/50'"
                                                class="w-2 h-2 rounded-full transition-all"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Miniatures --}}
                        @if($listing->images->count() > 1)
                            <div class="flex gap-2 p-3 overflow-x-auto">
                                @foreach($listing->images as $index => $image)
                                    <button @click="active = {{ $index }}"
                                            :class="active === {{ $index }} ? 'ring-2 ring-gold' : 'opacity-60 hover:opacity-100'"
                                            class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden transition-all">
                                        <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="h-72 bg-sand flex items-center justify-center">
                            <svg class="w-16 h-16 text-ink/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- En-tête de l'annonce --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-sand/60">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                        <div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="bg-gold/10 text-gold text-xs font-semibold px-3 py-1 rounded-full">
                                    {{ $listing->transaction_label }}
                                </span>
                                <span class="bg-sand text-ink/60 text-xs font-medium px-3 py-1 rounded-full">
                                    {{ $listing->property_label }}
                                </span>
                                @if($listing->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        En attente de validation
                                    </span>
                                @endif
                                @if($listing->isCurrentlySponsored())
                                    <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        ⭐ Annonce sponsorisee
                                    </span>
                                @endif
                            </div>
                            <h1 class="font-display text-3xl font-bold text-ink leading-tight">{{ $listing->title }}</h1>
                        </div>

                        {{-- Favori --}}
                        @auth
                            <div class="relative">
                                <x-favorite-button :listing="$listing" class="static" />
                            </div>
                        @endauth
                    </div>

                    <p class="text-ink/50 flex items-center gap-1.5 text-sm mb-4">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        {{ $listing->address ?? ($listing->zone ? $listing->zone . ', ' : '') . $listing->city }}
                    </p>

                    <div>
                        <div class="font-display text-4xl font-bold text-gold">
                            {{ $listing->formatted_price }}
                        </div>
                        @if($listing->surface && $listing->property_type === 'terrain')
                            <div class="text-sm font-medium text-ink/50 mt-1">
                                Soit {{ number_format($listing->price / $listing->surface, 0, ',', ' ') }} MAD/m²
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Caractéristiques --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-sand/60">
                    <h2 class="font-display text-xl font-semibold text-ink mb-5">Caractéristiques</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @if($listing->surface)
                            <div class="bg-sand rounded-xl p-4 text-center">
                                <svg class="w-6 h-6 mx-auto text-gold/60 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                <div class="font-semibold text-ink">{{ $listing->surface }} m²</div>
                                <div class="text-xs text-ink/50">Surface</div>
                            </div>
                        @endif
                        @if($listing->rooms)
                            <div class="bg-sand rounded-xl p-4 text-center">
                                <svg class="w-6 h-6 mx-auto text-gold/60 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11V9a4 4 0 014-4h10a4 4 0 014 4v2m-18 8v-8h18v8M3 15h18"/></svg>
                                <div class="font-semibold text-ink">{{ $listing->rooms }} chambres</div>
                                <div class="text-xs text-ink/50">Pièces</div>
                            </div>
                        @endif
                        @if($listing->bathrooms)
                            <div class="bg-sand rounded-xl p-4 text-center">
                                <svg class="w-6 h-6 mx-auto text-gold/60 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v2H2V6c0-1.1.9-2 2-2zM2 8h20v9c0 2.2-1.8 4-4 4H6c-2.2 0-4-1.8-4-4V8zM8 4V2m8 2V2M12 4V2"/></svg>
                                <div class="font-semibold text-ink">{{ $listing->bathrooms }}</div>
                                <div class="text-xs text-ink/50">Salles de bain</div>
                            </div>
                        @endif
                        @if($listing->floor !== null)
                            <div class="bg-sand rounded-xl p-4 text-center">
                                <svg class="w-6 h-6 mx-auto text-gold/60 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                                <div class="font-semibold text-ink">{{ $listing->floor === 0 ? 'RDC' : $listing->floor . 'ème' }}</div>
                                <div class="text-xs text-ink/50">Étage</div>
                            </div>
                        @endif
                    </div>

                    {{-- Options --}}
                    @php
                        $options = [
                            'furnished' => ['icon' => '<path d="M4 11V9a2 2 0 012-2h12a2 2 0 012 2v2m-14 5h14m2-5v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="2"/>', 'label' => 'Meublé'],
                            'parking'   => ['icon' => '<path d="M5 18a2 2 0 104 0 2 2 0 00-4 0zM15 18a2 2 0 104 0 2 2 0 00-4 0zM5 18H4v-4l1.5-4.5h13L20 14v4h-1M5 14h14M8 10V6a2 2 0 012-2h4a2 2 0 012 2v4" stroke-width="2"/>', 'label' => 'Parking'],
                            'elevator'  => ['icon' => '<path d="M7 9l5-5 5 5M7 15l5 5 5-5" stroke-width="2"/>', 'label' => 'Ascenseur'],
                            'pool'      => ['icon' => '<path d="M21 12c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2M21 16c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2" stroke-width="2"/>', 'label' => 'Piscine'],
                            'garden'    => ['icon' => '<path d="M12 21a9 9 0 009-9 9 9 0 00-9-9 9 9 0 00-9 9 9 9 0 009 9zM12 3v18" stroke-width="2"/>', 'label' => 'Jardin'],
                            'terrace'   => ['icon' => '<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707" stroke-width="2"/>', 'label' => 'Terrasse'],
                            'security'  => ['icon' => '<path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622" stroke-width="2"/>', 'label' => 'Sécurité'],
                        ];
                        $hasOptions = collect($options)->keys()->some(fn($k) => $listing->$k);
                    @endphp

                    @if($hasOptions)
                        <div class="border-t border-sand mt-5 pt-5">
                            <h3 class="text-sm font-semibold text-ink/60 uppercase tracking-wider mb-3">Équipements</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($options as $key => $opt)
                                    @if($listing->$key)
                                        <span class="flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-medium px-3 py-1.5 rounded-full border border-green-200">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $opt['icon'] !!}
                                            </svg>
                                            {{ $opt['label'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-sand/60">
                    <h2 class="font-display text-xl font-semibold text-ink mb-4">Description</h2>
                    <div class="prose prose-sm max-w-none text-ink/70 leading-relaxed">
                        {!! nl2br(e($listing->description)) !!}
                    </div>
                </div>

                {{-- Carte Google Maps --}}
                @if($listing->latitude && $listing->longitude)
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-sand/60">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
                            <div>
                                <h2 class="font-display text-xl font-semibold text-ink">Localisation</h2>
                                <p class="text-sm text-ink/50 mt-1">Visualisez le bien directement dans Google Maps et ouvrez un itinéraire en un clic.</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $listing->google_maps_url }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 rounded-xl bg-ink px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-ink/85">
                                    Ouvrir Google Maps
                                </a>
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $listing->latitude }},{{ $listing->longitude }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 rounded-xl border border-sand px-4 py-2.5 text-sm font-semibold text-ink transition hover:border-gold hover:text-gold">
                                    Itinéraire
                                </a>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-2xl border border-sand/80 bg-sand-light">
                            <iframe src="{{ $listing->google_maps_embed_url }}"
                                    class="h-72 w-full border-0"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                @endif

                {{-- Signaler --}}
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-sand/60">
                    <details class="group">
                        <summary class="flex items-center gap-2 text-sm text-ink/50 cursor-pointer hover:text-ink/70">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Signaler cette annonce
                        </summary>
                        <div class="mt-4">
                            @auth
                                <form action="{{ route('listings.report', $listing) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <select name="reason" required class="w-full px-3 py-2 bg-sand rounded-lg text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink">
                                        <option value="">Raison du signalement...</option>
                                        @foreach(\App\Models\Report::REASONS as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="description" rows="2" placeholder="Détails (optionnel)"
                                              class="w-full px-3 py-2 bg-sand rounded-lg text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink resize-none placeholder:text-ink/40"></textarea>
                                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                                        Envoyer le signalement
                                    </button>
                                </form>
                            @else
                                <div class="bg-sand rounded-lg p-3 text-center text-sm text-ink/60">
                                    Vous devez être connecté pour signaler une annonce.<br>
                                    <a href="{{ route('login') }}" class="text-gold hover:underline font-medium mt-1 inline-block">Se connecter</a>
                                </div>
                            @endauth
                        </div>
                    </details>
                </div>
            </div>

            {{-- ── Sidebar droite (1/3) ── --}}
            <div class="space-y-5">

                {{-- Contact agent --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-sand/60 sticky top-24">
                    {{-- Profil du vendeur --}}
                    <div class="flex items-center gap-3 mb-5 pb-5 border-b border-sand">
                        <img src="{{ $listing->user->avatar_url }}" alt="{{ $listing->user->name }}"
                             class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <p class="font-semibold text-ink text-sm">Agent Sarouty</p>
                            <p class="text-xs text-ink/50">{{ $listing->user->role_label }}</p>
                        </div>
                    </div>

                    <h3 class="font-display text-xl font-semibold text-ink mb-4">Contacter</h3>

                    @if(session('success'))
                        <div class="bg-green-50 text-green-700 text-sm p-3 rounded-xl mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @auth
                        <form action="{{ route('listings.contact', $listing) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="text" name="sender_name"
                                   value="{{ old('sender_name', auth()->user()?->name ?? '') }}"
                                   placeholder="Votre nom *" required
                                   class="w-full px-4 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                            <input type="email" name="sender_email"
                                   value="{{ old('sender_email', auth()->user()?->email ?? '') }}"
                                   placeholder="Votre email *" required
                                   class="w-full px-4 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                            <input type="tel" name="sender_phone"
                                   value="{{ old('sender_phone', auth()->user()?->phone ?? '') }}"
                                   placeholder="Votre téléphone"
                                   class="w-full px-4 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                            <textarea name="message" rows="4" required
                                      placeholder="Bonjour, je suis intéressé(e) par votre bien..."
                                      class="w-full px-4 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink resize-none placeholder:text-ink/40">{{ old('message', "Bonjour, je suis intéressé(e) par votre annonce « {$listing->title} ». Pourriez-vous me donner plus d'informations ?") }}</textarea>
                            <button type="submit"
                                    class="w-full bg-gold hover:bg-gold-dark text-white font-semibold py-3 rounded-xl transition-colors">
                                Envoyer le message
                            </button>
                        </form>
                    @else
                        <div class="text-center py-6 bg-sand rounded-xl">
                            <svg class="w-12 h-12 text-gold/50 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <h4 class="font-semibold text-ink mb-1">Pour contacter l'annonceur</h4>
                            <p class="text-xs text-ink/60 mb-4 px-4">Vous devez vous connecter en tant que client pour envoyer un message.</p>
                            <a href="{{ route('login') }}" class="inline-block bg-gold hover:bg-gold-dark text-white font-semibold py-2.5 px-6 rounded-xl transition-colors text-sm">
                                Se connecter
                            </a>
                        </div>
                    @endauth

                    <div class="mt-4 pt-4 border-t border-sand text-xs text-ink/40 text-center space-y-2">
                        <p class="flex items-center justify-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ number_format($listing->views) }} vue(s)
                        </p>
                        <p class="flex items-center justify-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Publiée le {{ $listing->created_at->format('d/m/Y') }}
                        </p>
                        <p class="flex items-center justify-center gap-1.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                            Réf. DM-{{ str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Annonces similaires ── --}}
        @if($similarListings->isNotEmpty())
            <div class="mt-16">
                <h2 class="font-display text-3xl font-bold text-ink mb-8">Annonces similaires</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($similarListings as $listing)
                        @include('components.listing-card', ['listing' => $listing])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

