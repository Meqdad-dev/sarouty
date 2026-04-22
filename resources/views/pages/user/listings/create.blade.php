@extends('layouts.user')

@section('title', isset($listing) ? 'Modifier l\'annonce' : 'Publier une annonce')
@section('page_title', isset($listing) ? 'Modifier l\'annonce' : 'Publier une annonce')
@section('page_subtitle', isset($listing) ? 'Mettez à jour les informations de votre bien.' : 'Remplissez les informations de votre bien. Notre équipe le validera sous 24h.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('top_actions')
    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
        Annuler
    </a>
@endsection

@section('content')
<div x-data="listingForm({{ $maxImages ?? 5 }})" x-init="init()" class="max-w-4xl mb-12">
    <form action="{{ isset($listing) ? route('user.listings.update', $listing) : route('user.listings.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if(isset($listing)) @method('PUT') @endif

        {{-- ── SECTION 1 : Informations principales ──────────────────── --}}
        <div class="panel rounded-[24px] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                <span class="w-7 h-7 bg-gold text-white rounded-full flex items-center justify-center text-sm font-bold shadow-sm">1</span>
                <h2 class="font-semibold text-gray-900 dark:text-white">Informations principales</h2>
            </div>
            <div class="p-6 space-y-6">

                {{-- Type transaction + Bien --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type d'opération *</label>
                        <select name="transaction_type" x-model="form.transaction_type"
                                class="w-full border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                            @foreach($transactionTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('transaction_type', $listing->transaction_type ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('transaction_type')
                            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de bien *</label>
                        <select name="property_type" x-model="form.property_type"
                                class="w-full border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                            @foreach($propertyTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('property_type', $listing->property_type ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_type')
                            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Titre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Titre de l'annonce *</label>
                    <input type="text" name="title" x-model="form.title"
                           value="{{ old('title', $listing->title ?? '') }}"
                           placeholder="Ex: Magnifique appartement vue mer à Tanger"
                           class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow"
                           maxlength="150">
                    <div class="flex justify-between mt-1.5">
                        <span class="text-xs text-gray-400">Minimum 10 caractères</span>
                        <span class="text-xs text-gray-400" x-text="(form.title?.length ?? 0) + '/150'"></span>
                    </div>
                    @error('title')
                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description + bouton IA --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Description *</label>
                        <button type="button" @click="generateDescription()"
                                :disabled="aiLoading || !form.title"
                                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all
                                       bg-gradient-to-r from-violet-500 to-indigo-500 text-white hover:from-violet-600 hover:to-indigo-600
                                       disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 :class="aiLoading ? 'animate-spin' : ''">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                            <span x-text="aiLoading ? 'Génération...' : 'Décrire depuis le titre'"></span>
                        </button>
                    </div>
                    <p x-show="!form.title" class="text-xs text-amber-600 dark:text-amber-400 mb-2 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Renseignez d'abord le titre de l'annonce pour laisser l'IA rédiger la description.
                    </p>
                    <textarea name="description" x-model="form.description" rows="6"
                              placeholder="Décrivez votre bien : emplacement, état, atouts, environnement..."
                              class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none resize-none transition-colors"
                              :class="aiLoading ? 'bg-violet-50/50 dark:bg-violet-900/20 border-violet-200 dark:border-violet-800' : ''"
                    >{{ old('description', $listing->description ?? '') }}</textarea>
                    <div x-show="aiError" class="mt-1.5 text-xs text-red-500" x-text="aiError"></div>
                    @error('description')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prix + Surface (Dans Informations générales) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prix (MAD) *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="price" x-model="form.price"
                                   value="{{ old('price', $listing->price ?? '') }}"
                                   placeholder="Ex: 1 500 000"
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                            
                            <template x-if="form.transaction_type === 'location' || form.transaction_type === 'vacances'">
                                <select name="price_period" x-model="form.price_period"
                                        class="w-32 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                    @foreach($pricePeriods ?? \App\Models\Listing::PRICE_PERIODS as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </template>
                        </div>
                        @error('price')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                        @error('price_period')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Surface (m²) *</label>
                        <input type="number" name="surface" x-model="form.surface"
                               value="{{ old('surface', $listing->surface ?? '') }}"
                               placeholder="Ex: 120"
                               class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                        @error('surface')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>





                {{-- ── Caractéristiques spécifiques au type de bien ── --}}
                <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 p-5 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Caractéristiques du bien
                        <span class="text-xs text-gray-400 font-normal" x-text="'(' + (form.property_type || 'non sélectionné') + ')'"></span>
                    </h3>

                    {{-- Pièces --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-4 border-b border-gray-200 dark:border-gray-700" x-show="['appartement','villa','riad','bureau','local'].includes(form.property_type)">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pièces</label>
                            <input type="number" name="rooms" x-model="form.rooms"
                                   value="{{ old('rooms', $listing->rooms ?? '') }}"
                                   placeholder="Ex: 3"
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                            @error('rooms')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Appartement --}}
                    <div x-show="form.property_type === 'appartement'" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Salles de bain</label>
                            <select name="bathrooms" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('bathrooms', $listing->bathrooms ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Étage</label>
                            <select name="floor" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                <option value="0" {{ old('floor', $listing->floor ?? '') == 0 ? 'selected' : '' }}>Rez-de-chaussée</option>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('floor', $listing->floor ?? '') == $i ? 'selected' : '' }}>{{ $i }}ème étage</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">État du bien</label>
                            <select name="condition" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                <option value="neuf" {{ old('condition', $listing->condition ?? '') === 'neuf' ? 'selected' : '' }}>Neuf</option>
                                <option value="excellent" {{ old('condition', $listing->condition ?? '') === 'excellent' ? 'selected' : '' }}>Excellent état</option>
                                <option value="bon" {{ old('condition', $listing->condition ?? '') === 'bon' ? 'selected' : '' }}>Bon état</option>
                                <option value="a_renover" {{ old('condition', $listing->condition ?? '') === 'a_renover' ? 'selected' : '' }}>À rénover</option>
                            </select>
                        </div>
                    </div>

                    {{-- Villa --}}
                    <div x-show="form.property_type === 'villa'" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Salles de bain</label>
                            <select name="bathrooms" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ old('bathrooms', $listing->bathrooms ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Surface terrain (m²)</label>
                            <input type="number" name="land_surface" value="{{ old('land_surface', $listing->land_surface ?? '') }}" placeholder="500" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Niveaux / Étages</label>
                            <select name="floor" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('floor', $listing->floor ?? '') == $i ? 'selected' : '' }}>{{ $i }} niveau(x)</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Terrain --}}
                    <div x-show="form.property_type === 'terrain'" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Nature du terrain</label>
                            <select name="land_type" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                <option value="constructible" {{ old('land_type', $listing->land_type ?? '') === 'constructible' ? 'selected' : '' }}>Constructible</option>
                                <option value="agricole" {{ old('land_type', $listing->land_type ?? '') === 'agricole' ? 'selected' : '' }}>Agricole</option>
                                <option value="industriel" {{ old('land_type', $listing->land_type ?? '') === 'industriel' ? 'selected' : '' }}>Industriel</option>
                                <option value="autre" {{ old('land_type', $listing->land_type ?? '') === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Façade (ml)</label>
                            <input type="number" name="facade" value="{{ old('facade', $listing->facade ?? '') }}" placeholder="20" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">COS / CUS</label>
                            <input type="text" name="cos" value="{{ old('cos', $listing->cos ?? '') }}" placeholder="Ex: R+3, 60%" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                        </div>
                    </div>

                    {{-- Riad --}}
                    <div x-show="form.property_type === 'riad'" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Salles de bain</label>
                            <select name="bathrooms" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('bathrooms', $listing->bathrooms ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Nombre de patios</label>
                            <select name="patios" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('patios', $listing->patios ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Usage</label>
                            <select name="riad_usage" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                <option value="habitation" {{ old('riad_usage', $listing->riad_usage ?? '') === 'habitation' ? 'selected' : '' }}>Habitation</option>
                                <option value="maison_hotes" {{ old('riad_usage', $listing->riad_usage ?? '') === 'maison_hotes' ? 'selected' : '' }}>Maison d'hôtes</option>
                                <option value="investissement" {{ old('riad_usage', $listing->riad_usage ?? '') === 'investissement' ? 'selected' : '' }}>Investissement</option>
                            </select>
                        </div>
                    </div>

                    {{-- Bureau / Local : caractéristiques commerciales --}}
                    <div x-show="['bureau','local','entrepot'].includes(form.property_type)" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Étage</label>
                            <select name="floor" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                <option value="0" {{ old('floor', $listing->floor ?? '') == 0 ? 'selected' : '' }}>Rez-de-chaussée</option>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('floor', $listing->floor ?? '') == $i ? 'selected' : '' }}>{{ $i }}ème étage</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Hauteur sous plafond (m)</label>
                            <input type="number" step="0.1" name="ceiling_height" value="{{ old('ceiling_height', $listing->ceiling_height ?? '') }}" placeholder="3.5" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Type de local</label>
                            <select name="commercial_type" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                                <option value="">—</option>
                                <option value="open_space" {{ old('commercial_type', $listing->commercial_type ?? '') === 'open_space' ? 'selected' : '' }}>Open space</option>
                                <option value="cloisonne" {{ old('commercial_type', $listing->commercial_type ?? '') === 'cloisonne' ? 'selected' : '' }}>Cloisonné</option>
                                <option value="mixte" {{ old('commercial_type', $listing->commercial_type ?? '') === 'mixte' ? 'selected' : '' }}>Mixte</option>
                            </select>
                        </div>
                    </div>

                    {{-- Message si aucun type sélectionné --}}
                    <div x-show="!form.property_type" class="text-center py-3 text-sm text-gray-400 dark:text-gray-500">
                        Sélectionnez un type de bien pour voir les caractéristiques détaillées.
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 2 : Localisation + Carte ──────────────────────── --}}
        <div class="panel rounded-[24px] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                <span class="w-7 h-7 bg-gold text-white rounded-full flex items-center justify-center text-sm font-bold shadow-sm">2</span>
                <h2 class="font-semibold text-gray-900 dark:text-white">Localisation</h2>
            </div>
            <div class="p-6 space-y-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ville *</label>
                        <select name="city" x-model="form.city" @change="updateMapCity()"
                                class="w-full border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                            <option value="">Choisir une ville</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ old('city', $listing->city ?? '') === $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                        @error('city')
                            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quartier / Zone</label>
                        <select name="zone" x-model="form.zone" :disabled="!availableZones.length"
                                class="w-full border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow disabled:opacity-50">
                            <option value="">Choisir un quartier</option>
                            <template x-for="z in availableZones" :key="z">
                                <option :value="z" x-text="z" :selected="form.zone === z"></option>
                            </template>
                        </select>
                        @error('zone')
                            <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse complète</label>
                    <div class="flex gap-2">
                        <input type="text" name="address" x-model="form.address"
                               value="{{ old('address', $listing->address ?? '') }}"
                               placeholder="Rue, numéro…"
                               class="flex-1 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none transition-shadow">
                        <button type="button" @click="geocodeAddress()"
                                :disabled="geoLoading || !form.city"
                                class="px-5 py-3 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-xl text-sm font-medium hover:opacity-90 transition-opacity disabled:opacity-50 whitespace-nowrap shadow-sm flex items-center gap-2">
                            <svg x-show="!geoLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-show="!geoLoading">Localiser</span>
                            <span x-show="geoLoading">...</span>
                        </button>
                    </div>
                    @error('address')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Champs cachés coords --}}
                <input type="hidden" name="latitude"  x-model="form.lat">
                <input type="hidden" name="longitude" x-model="form.lng">

                {{-- Carte OpenStreetMap --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Position sur la carte</label>
                        <span class="text-xs text-gray-400">Cliquez sur la carte pour ajuster le marqueur</span>
                    </div>
                    <div id="createMap" class="h-[280px] rounded-[16px] border border-gray-200 dark:border-gray-700 overflow-hidden z-10 relative"></div>
                    <div x-show="locationDisplay || geoMessage" class="mt-3 rounded-2xl border border-sky-100 bg-sky-50/80 px-4 py-3 text-sm text-sky-800 dark:border-sky-900/40 dark:bg-sky-950/20 dark:text-sky-200">
                        <div x-show="locationDisplay" class="font-medium" x-text="locationDisplay"></div>
                        <div x-show="geoMessage" class="mt-1 text-xs text-sky-700 dark:text-sky-300" x-text="geoMessage"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 3 : Équipements ─────────────────────────────── --}}
        <div class="panel rounded-[24px] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                <span class="w-7 h-7 bg-gold text-white rounded-full flex items-center justify-center text-sm font-bold shadow-sm">3</span>
                <h2 class="font-semibold text-gray-900 dark:text-white">Équipements & caractéristiques</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach([
                        ['name' => 'furnished', 'label' => 'Meublé',      'icon' => '<path d="M4 11V9a2 2 0 012-2h12a2 2 0 012 2v2m-14 5h14m2-5v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="2"/>'],
                        ['name' => 'parking',   'label' => 'Parking',     'icon' => '<path d="M5 18a2 2 0 104 0 2 2 0 00-4 0zM15 18a2 2 0 104 0 2 2 0 00-4 0zM5 18H4v-4l1.5-4.5h13L20 14v4h-1M5 14h14M8 10V6a2 2 0 012-2h4a2 2 0 012 2v4" stroke-width="2"/>'],
                        ['name' => 'elevator',  'label' => 'Ascenseur',   'icon' => '<path d="M7 9l5-5 5 5M7 15l5 5 5-5" stroke-width="2"/>'],
                        ['name' => 'pool',      'label' => 'Piscine',     'icon' => '<path d="M21 12c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2M21 16c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2" stroke-width="2"/>'],
                        ['name' => 'garden',    'label' => 'Jardin',      'icon' => '<path d="M12 21a9 9 0 009-9 9 9 0 00-9-9 9 9 0 00-9 9 9 9 0 009 9zM12 3v18" stroke-width="2"/>'],
                        ['name' => 'terrace',   'label' => 'Terrasse',    'icon' => '<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707" stroke-width="2"/>'],
                        ['name' => 'security',  'label' => 'Gardiennage', 'icon' => '<path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622" stroke-width="2"/>'],
                    ] as $feat)
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-gold/50 dark:hover:border-gold/50 cursor-pointer transition-colors group"
                               :class="form.{{ $feat['name'] }} ? 'bg-gold/5 dark:bg-gold/10 border-gold/50 dark:border-gold/50' : ''">
                            <input type="checkbox" name="{{ $feat['name'] }}" value="1"
                                   x-model="form.{{ $feat['name'] }}"
                                   {{ old($feat['name'], ($listing->{$feat['name']} ?? false) ? '1' : '') ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $feat['icon'] !!}
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors"
                                  :class="form.{{ $feat['name'] }} ? 'text-gray-900 dark:text-white' : ''">{{ $feat['label'] }}</span>
                            <span x-show="form.{{ $feat['name'] }}" class="ml-auto text-gold text-xs font-bold">✓</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── SECTION 4 : Durée & sponsorisation ───────────────────── --}}
        <div class="panel rounded-[24px] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                <span class="w-7 h-7 bg-gold text-white rounded-full flex items-center justify-center text-sm font-bold shadow-sm">4</span>
                <h2 class="font-semibold text-gray-900 dark:text-white">Durée & sponsorisation</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 dark:bg-emerald-900/10 dark:border-emerald-800 p-4">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Durée actuelle de l'annonce</div>
                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Votre formule <span class="font-semibold">{{ auth()->user()->plan_label }}</span> donne une durée de
                        <span class="font-semibold text-emerald-700 dark:text-emerald-400">{{ $listingDurationDays }} jours</span>
                        pour chaque annonce.
                    </div>
                </div>

                @if($canCreateSponsoredListing)
                    <label class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50/80 dark:bg-amber-900/10 dark:border-amber-800 p-4 cursor-pointer hover:border-gold/60 transition-colors">
                        <input type="checkbox" name="is_sponsored_requested" value="1"
                               x-model="form.is_sponsored_requested"
                               {{ old('is_sponsored_requested', request('sponsored', ($listing->is_sponsored ?? false) ? '1' : '')) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 rounded border-gray-300 text-gold focus:ring-gold">
                        <div>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                                <span>⭐</span>
                                <span>Sponsored</span>
                                <span class="inline-flex items-center rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">{{ auth()->user()->plan_label }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                Votre abonnement permet une sponsorisation incluse pendant
                                <span class="font-semibold text-amber-700 dark:text-amber-400">{{ $sponsoredDurationDays }} jours</span>.
                                L'annonce sera marquée par une étoile dans l'espace admin, la liste d'attente et les annonces publiques après validation.
                            </p>
                        </div>
                    </label>
                @else
                    <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-4 text-sm text-gray-600 dark:text-gray-300">
                        <div class="font-semibold text-gray-900 dark:text-white">Sponsorisation non disponible avec votre plan actuel</div>
                        <p class="mt-1">
                            Passez à une formule <span class="font-semibold">Starter, Pro ou Agence</span> pour créer une annonce sponsorisée avec étoile.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── SECTION 5 : Photos ──────────────────────────────────── --}}
        <div class="panel rounded-[24px] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center gap-3">
                <span class="w-7 h-7 bg-gold text-white rounded-full flex items-center justify-center text-sm font-bold shadow-sm">5</span>
                <h2 class="font-semibold text-gray-900 dark:text-white">Photos</h2>
            </div>
            <div class="p-6">
                @if(isset($listing) && $listing->images->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        @foreach($listing->images as $img)
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-sm">
                                <img src="{{ asset('storage/' . $img->path) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-medium">Photo {{ $loop->index + 1 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-[16px] cursor-pointer hover:border-gold/50 hover:bg-gold/5 transition-all text-center px-4">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cliquez ou déposez vos photos</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="`JPG, PNG, WebP · Max 5 MB · ${maxImages} photos max`"></span>
                    <input type="file" name="images[]" multiple accept="image/*" class="sr-only"
                           @change="previewImages($event)">
                </label>
                
                <div class="mt-4 flex gap-2">
                    <input type="url" x-model="tempUrl" @keydown.enter.prevent="addUrlAsPreview()" placeholder="Ou coller l'URL d'une image (ex: https://...)" class="flex-1 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold outline-none">
                    <button type="button" @click="addUrlAsPreview()" :disabled="!tempUrl" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-xl text-sm font-semibold transition-colors disabled:opacity-50">Ajouter</button>
                </div>
                @error('images')
                    <p class="mt-2 text-sm text-red-500 font-medium text-center">{{ $message }}</p>
                @enderror
                @if($errors->has('images.*'))
                    <div class="mt-2 text-sm text-red-500 font-medium text-center">
                        @foreach($errors->get('images.*') as $errorGroup)
                            @foreach($errorGroup as $errorMsg)
                                <p>{{ $errorMsg }}</p>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                {{-- Prévisualisation --}}
                <div x-show="previews.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                            <img :src="src" class="w-full h-full object-cover">
                            <template x-if="src.startsWith('http')">
                                <input type="hidden" name="image_urls[]" :value="src">
                            </template>
                            <button type="button" @click="removePreview(i)"
                                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-sm flex items-center justify-center hover:bg-red-600 shadow-sm transition-colors transform hover:scale-110">
                                ×
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end pt-4">
            <button type="submit"
                    class="px-8 py-4 bg-gold text-white rounded-xl font-semibold hover:bg-gold-dark transition-colors shadow-md shadow-gold/20 flex items-center gap-2">
                {{ isset($listing) ? 'Enregistrer les modifications' : 'Soumettre l\'annonce' }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function listingForm(maxImagesLimit = 5) {
    return {
        maxImages: maxImagesLimit,
        form: {
            transaction_type: '{{ old("transaction_type", $listing->transaction_type ?? "vente") }}',
            property_type:    '{{ old("property_type",    $listing->property_type    ?? "appartement") }}',
            title:       '{{ old("title",       $listing->title       ?? "") }}',
            description: `{{ old("description", addslashes($listing->description ?? "")) }}`,
            price:       '{{ old("price",       $listing->price       ?? "") }}',
            price_period:'{{ old("price_period", $listing->price_period ?? "mois") }}',
            surface:     '{{ old("surface",     $listing->surface     ?? "") }}',
            rooms:       '{{ old("rooms",       $listing->rooms       ?? "") }}',
            city:        '{{ old("city",        $listing->city        ?? "") }}',
            zone:        '{{ old("zone",        $listing->zone        ?? "") }}',
            address:     '{{ old("address",     $listing->address     ?? "") }}',
            lat:         '{{ old("latitude",    $listing->latitude    ?? "") }}',
            lng:         '{{ old("longitude",   $listing->longitude   ?? "") }}',
            furnished:   {{ old("furnished",  ($listing->furnished  ?? false) ? "true" : "false") }},
            parking:     {{ old("parking",    ($listing->parking    ?? false) ? "true" : "false") }},
            elevator:    {{ old("elevator",   ($listing->elevator   ?? false) ? "true" : "false") }},
            pool:        {{ old("pool",       ($listing->pool       ?? false) ? "true" : "false") }},
            garden:      {{ old("garden",     ($listing->garden     ?? false) ? "true" : "false") }},
            terrace:     {{ old("terrace",    ($listing->terrace    ?? false) ? "true" : "false") }},
            security:    {{ old("security",   ($listing->security   ?? false) ? "true" : "false") }},
            is_sponsored_requested: {{ old("is_sponsored_requested", request('sponsored', ($listing->is_sponsored ?? false) ? "true" : "false")) }},
        },
        aiLoading: false,
        aiError: '',
        geoLoading: false,
        locationDisplay: '',
        geoMessage: '',
        previews: [],
        tempUrl: '',
        map: null,
        marker: null,

        // Coordonnées des villes marocaines
        cityCoords: {
            'Casablanca': [33.5731, -7.5898], 'Rabat': [34.0209, -6.8416],
            'Marrakech':  [31.6295, -7.9811], 'Fès':   [34.0181, -5.0078],
            'Tanger':     [35.7595, -5.8340], 'Agadir':[30.4278, -9.5981],
            'Meknès':     [33.8931, -5.5473], 'Oujda': [34.6814, -1.9086],
        },

        // Quartiers / Zones par ville
        cityZones: {
            'Casablanca': ['Maârif', 'Gauthier', 'Anfa', 'Bourgogne', 'Sidi Belyout', 'Ain Diab', 'Oulfa', 'Bernoussi', 'Ain Sebaa', 'Hay Hassani'],
            'Rabat': ['Agdal', 'Hassan', 'Hay Riad', 'Souissi', 'Akkari', 'Yacoub El Mansour', 'Medina'],
            'Marrakech': ['Guéliz', 'Hivernage', 'Médina', 'Palmeraire', 'Daoudiate', 'Sidi Youssef Ben Ali', 'Targa', 'Victor Hugo'],
            'Tanger': ['Malabata', 'Iberia', 'Marchan', 'Centre Ville', 'Boubana', 'Val Fleuri'],
            'Agadir': ['Founty', 'Sonaba', 'Talborjt', 'Haut Founty', 'Charaf', 'Hay Mohammadi'],
            'Fès': ['Ville Nouvelle', 'Médina', 'Agdal', 'Narjiss', 'Saiss'],
            'Meknès': ['Hamria', 'Médina', 'Plaisance', 'Toulal', 'Bassatine'],
            'Oujda': ['Centre Ville', 'Hay Lazaret', 'Al Qods', 'Village Touba'],
        },

        get availableZones() {
            return this.form.city ? (this.cityZones[this.form.city] || []) : [];
        },

        init() {
            this.$nextTick(() => { this.initMap(); });
        },

        initMap() {
            const lat = parseFloat(this.form.lat) || 31.7917;
            const lng = parseFloat(this.form.lng) || -7.0926;
            const zoom = this.form.lat ? 14 : 6;

            this.map = L.map('createMap').setView([lat, lng], zoom);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap contributors © CARTO',
                maxZoom: 19,
            }).addTo(this.map);

            if (this.form.lat) {
                this.placeMarker(lat, lng);
            }

            this.map.on('click', (e) => {
                this.placeMarker(e.latlng.lat, e.latlng.lng);
            });
        },

        placeMarker(lat, lng) {
            if (this.marker) this.marker.remove();
            const icon = L.divIcon({
                html: `<div style="background:#C8963E;width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3)"></div>`,
                iconSize: [16, 16], iconAnchor: [8, 8], className: ''
            });
            this.marker = L.marker([lat, lng], { icon, draggable: true }).addTo(this.map);
            this.form.lat = lat.toFixed(7);
            this.form.lng = lng.toFixed(7);
            this.marker.on('dragend', (e) => {
                const p = e.target.getLatLng();
                this.form.lat = p.lat.toFixed(7);
                this.form.lng = p.lng.toFixed(7);
            });
        },

        updateMapCity() {
            this.form.zone = ''; // Initialiser le quartier lors du changement de ville
            const coords = this.cityCoords[this.form.city];
            if (coords && this.map) {
                this.map.setView(coords, 12);
                if (!this.form.lat) this.placeMarker(coords[0], coords[1]);
            }
        },

        async geocodeAddress() {
            if (!this.form.city) return;
            this.geoLoading = true;
            this.geoMessage = '';
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch('{{ route("user.ai.geocode-address") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        city: this.form.city,
                        zone: this.form.zone,
                        address: this.form.address,
                    }),
                });
                
                if (!res.ok) {
                    if (res.status === 422) {
                        this.geoMessage = 'Veuillez vérifier les informations saisies.';
                        return;
                    }
                    throw new Error(`Erreur serveur (${res.status})`);
                }

                const data = await res.json();
                if (data.lat && data.lng) {
                    const lat = parseFloat(data.lat);
                    const lng = parseFloat(data.lng);
                    this.placeMarker(lat, lng);
                    this.map.setView([lat, lng], data.precision === 'exact' ? 16 : 13);
                    this.locationDisplay = data.display || [this.form.address, this.form.zone, this.form.city].filter(Boolean).join(', ');
                    this.geoMessage = data.message || '';
                } else {
                    this.geoMessage = data.error || 'Position introuvable pour cette adresse précise.';
                }
            } catch(e) {
                console.error('Geocoding error:', e);
                // Si la requête échoue ou l'API Nominatim est bloquée (CORS/Network), on simule quand même avec une ville par défaut.
                this.geoMessage = 'Service de localisation indisponible. Positionnement manuel requis sur la carte.';
            } finally {
                this.geoLoading = false;
            }
        },

        async generateDescription() {
            if (!this.form.title) return;
            this.aiLoading = true;
            this.aiError   = '';
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res   = await fetch('{{ route("user.ai.generate-description") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title:     this.form.title,
                        property_type:    this.form.property_type,
                        transaction_type: this.form.transaction_type,
                        city:      this.form.city,
                        zone:      this.form.zone,
                        surface:   this.form.surface,
                        rooms:     this.form.rooms,
                        price:     this.form.price,
                        furnished: this.form.furnished,
                        parking:   this.form.parking,
                        elevator:  this.form.elevator,
                        pool:      this.form.pool,
                        garden:    this.form.garden,
                        terrace:   this.form.terrace,
                        security:  this.form.security,
                    }),
                });
                const data = await res.json();
                if (data.description) {
                    this.form.description = data.description;
                } else {
                    this.aiError = data.error || 'Erreur de génération. Réessayez.';
                }
            } catch(e) {
                this.aiError = 'Service IA indisponible.';
            } finally {
                this.aiLoading = false;
            }
        },

        previewImages(event) {
            const files = Array.from(event.target.files);
            const remainingSlots = this.maxImages - this.previews.length;
            if (remainingSlots <= 0) return;
            
            files.slice(0, remainingSlots).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        removePreview(i) {
            this.previews.splice(i, 1);
        },

        addUrlAsPreview() {
            if (!this.tempUrl) return;
            if (this.previews.length >= this.maxImages) {
                alert(`Max ${this.maxImages} photos.`);
                return;
            }
            this.previews.push(this.tempUrl);
            this.tempUrl = '';
        },
    };
}
</script>
@endpush
