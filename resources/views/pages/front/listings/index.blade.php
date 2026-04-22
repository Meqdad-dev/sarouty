@extends('layouts.app')

@section('title', 'Toutes les annonces – Sarouty')

@section('content')
<div class="pt-20 min-h-screen bg-sand-light">

    {{-- En-tête --}}
    <div class="bg-ink py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-display text-4xl font-bold text-white mb-2">
                @if(isset($filters['city'])) Immobilier à {{ $filters['city'] }}
                @elseif(isset($filters['type'])) {{ \App\Models\Listing::TRANSACTION_TYPES[$filters['type']] ?? 'Annonces' }}
                @else Toutes les annonces
                @endif
            </h1>
            <p class="text-white/50 text-sm">{{ $listings->total() }} bien(s) trouvé(s)</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ───── Sidebar filtres ───── --}}
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-sand/60 p-6 sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-semibold text-ink">Filtres</h2>
                        <a href="{{ route('listings.index') }}" class="text-xs text-gold hover:underline">Réinitialiser</a>
                    </div>

                    <form action="{{ route('listings.index') }}" method="GET" id="filter-form" class="space-y-5">

                        {{-- Recherche textuelle --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Recherche</label>
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                   placeholder="Mots-clés..."
                                   class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Ville</label>
                            <select name="city" class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink">
                                <option value="">Toutes les villes</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ ($filters['city'] ?? '') === $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Transaction --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Transaction</label>
                            <select name="type" class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink">
                                <option value="">Tous types</option>
                                @foreach($transactionTypes as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['type'] ?? '') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Type de bien --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Type de bien</label>
                            <select name="property" class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink">
                                <option value="">Tous les biens</option>
                                @foreach($propertyTypes as $value => $label)
                                    <option value="{{ $value }}" {{ ($filters['property'] ?? '') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Budget --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Budget (MAD)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}"
                                       placeholder="Min"
                                       class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                                <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}"
                                       placeholder="Max"
                                       class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                            </div>
                        </div>

                        {{-- Surface --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Surface minimale (m²)</label>
                            <input type="number" name="min_surface" value="{{ $filters['min_surface'] ?? '' }}"
                                   placeholder="Ex: 80"
                                   class="w-full px-3 py-2.5 bg-sand rounded-xl text-sm border-0 focus:ring-2 focus:ring-gold/30 text-ink placeholder:text-ink/40">
                        </div>

                        {{-- Chambres --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Chambres min.</label>
                            <div class="flex gap-2">
                                @foreach(['1' => '1+', '2' => '2+', '3' => '3+', '4' => '4+', '5' => '5+'] as $val => $lbl)
                                    <button type="button"
                                            onclick="document.querySelector('[name=rooms]').value = (document.querySelector('[name=rooms]').value === '{{ $val }}' ? '' : '{{ $val }}'); this.parentElement.querySelectorAll('button').forEach(b => b.classList.remove('bg-gold','text-white','border-gold')); if(document.querySelector('[name=rooms]').value) { this.classList.add('bg-gold','text-white','border-gold'); }"
                                            class="flex-1 py-1.5 text-xs border rounded-lg transition-all {{ ($filters['rooms'] ?? '') === $val ? 'bg-gold text-white border-gold' : 'border-sand text-ink/60 hover:border-gold/40' }}">
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                                <input type="hidden" name="rooms" value="{{ $filters['rooms'] ?? '' }}">
                            </div>
                        </div>

                        {{-- Options --}}
                        <div>
                            <label class="block text-xs font-semibold text-ink/60 uppercase tracking-wider mb-2">Options</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="furnished" value="1"
                                           {{ request('furnished') ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-sand text-gold focus:ring-gold">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <svg class="w-4 h-4 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 11V9a2 2 0 012-2h12a2 2 0 012 2v2m-14 5h14m2-5v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="2"/></svg>
                                        <span class="text-sm text-ink truncate">Meublé</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="parking" value="1"
                                           {{ request('parking') ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-sand text-gold focus:ring-gold">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <svg class="w-4 h-4 text-ink/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 18a2 2 0 104 0 2 2 0 00-4 0zM15 18a2 2 0 104 0 2 2 0 00-4 0zM5 18H4v-4l1.5-4.5h13L20 14v4h-1M5 14h14M8 10V6a2 2 0 012-2h4a2 2 0 012 2v4" stroke-width="2"/></svg>
                                        <span class="text-sm text-ink truncate">Parking</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-gold hover:bg-gold-dark text-white font-semibold py-3 rounded-xl transition-colors">
                            Appliquer les filtres
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ───── Liste des annonces ───── --}}
            <div class="flex-1 min-w-0">

                {{-- Tri --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-ink/60">
                        <span class="font-semibold text-ink">{{ $listings->total() }}</span> annonce(s)
                    </p>
                    <select onchange="this.form.submit()" form="filter-form" name="sort"
                            class="text-sm bg-white border border-sand rounded-xl px-3 py-2 text-ink focus:ring-2 focus:ring-gold/30">
                        <option value="latest" {{ ($filters['sort'] ?? 'latest') === 'latest' ? 'selected' : '' }}>Plus récentes</option>
                        <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="popular" {{ ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>Plus vues</option>
                    </select>
                </div>

                @if($listings->isEmpty())
                    <div class="text-center py-20 bg-white rounded-2xl border border-sand/60">
                        <div class="flex justify-center mb-4">
                            <svg class="w-16 h-16 text-ink/10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-display text-2xl font-semibold text-ink mb-2">Aucune annonce trouvée</h3>
                        <p class="text-ink/50 mb-6">Essayez de modifier vos critères de recherche.</p>
                        <a href="{{ route('listings.index') }}" class="text-gold hover:underline text-sm font-medium">
                            Voir toutes les annonces
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($listings as $listing)
                            @include('components.listing-card', ['listing' => $listing])
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-10">
                        {{ $listings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
