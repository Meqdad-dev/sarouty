{{--
    Composant carte d'annonce réutilisable
    Usage : @include('components.listing-card', ['listing' => $listing])
--}}
<article class="listing-card group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-sand/60">

    {{-- Image --}}
    <a href="{{ route('listings.show', $listing) }}" class="block relative overflow-hidden h-52">
        <img src="{{ $listing->thumbnail_url }}"
             alt="{{ $listing->title }}"
             class="listing-img w-full h-full object-cover"
             loading="lazy">

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex gap-2 flex-wrap">
            <span class="bg-gold text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                {{ $listing->transaction_label }}
            </span>
            @if($listing->isCurrentlySponsored())
                <span class="bg-amber-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
                    <span aria-hidden="true">⭐</span>
                    Sponsorisee
                </span>
            @endif
            @if($listing->featured)
                <span class="bg-terracotta text-white text-xs font-semibold px-2.5 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Coup de cœur
                </span>
            @endif
        </div>

        {{-- Favori (AJAX) --}}
        @auth
            <x-favorite-button :listing="$listing" />
        @else
            <a href="{{ route('login') }}"
               class="absolute top-3 right-3 w-9 h-9 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-sm transition-all">
                <svg class="w-5 h-5 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </a>
        @endauth
    </a>

    {{-- Contenu --}}
    <div class="p-4">
        <div class="flex items-baseline justify-between mb-2">
            <span class="font-display text-2xl font-bold text-ink">{{ $listing->formatted_price }}</span>
            @if($listing->surface && $listing->property_type === 'terrain')
                <span class="text-xs text-ink/40 font-medium">
                    {{ number_format($listing->price / $listing->surface, 0, ',', ' ') }} MAD/m²
                </span>
            @endif
        </div>

        {{-- Titre --}}
        <a href="{{ route('listings.show', $listing) }}">
            <h3 class="font-semibold text-ink text-sm leading-snug mb-2 line-clamp-2 group-hover:text-gold transition-colors">
                @if($listing->isCurrentlySponsored())⭐ @endif{{ $listing->title }}
            </h3>
        </a>

        {{-- Localisation --}}
        <p class="text-xs text-ink/50 flex items-center gap-1 mb-3">
            <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            {{ $listing->zone ? $listing->zone . ', ' : '' }}{{ $listing->city }}
        </p>

        {{-- Caractéristiques --}}
        <div class="flex items-center gap-3 text-xs text-ink/60 border-t border-sand pt-3">
            @if($listing->surface)
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    {{ $listing->surface }} m²
                </span>
            @endif
            @if($listing->rooms)
                <span class="flex items-center gap-1" title="Chambres">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 7v10a1 1 0 001 1h16a1 1 0 001-1V7M3 7h18M5 7V3m14 4V3"/>
                    </svg>
                    {{ $listing->rooms }} ch.
                </span>
            @endif
            @if($listing->bathrooms)
                <span class="flex items-center gap-1" title="Salles de bain">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v2H2V6c0-1.1.9-2 2-2zM2 8h20v9c0 2.2-1.8 4-4 4H6c-2.2 0-4-1.8-4-4V8zM8 4V2m8 2V2M12 4V2"/>
                    </svg>
                    {{ $listing->bathrooms }}
                </span>
            @endif
            <span class="ml-auto flex items-center gap-0.5 text-ink/30">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($listing->views) }}
            </span>
        </div>
    </div>
</article>
