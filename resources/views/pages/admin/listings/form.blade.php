@extends('layouts.admin')

@section('title', ($listing->exists ? 'Modifier' : 'Créer') . ' une annonce – Sarouty')
@section('page_title', $listing->exists ? 'Modifier l\'annonce' : 'Créer une annonce')
@section('page_subtitle', $listing->exists ? "DM-{$listing->id}: {$listing->title}" : 'Remplissez les informations pour créer une nouvelle annonce')

@section('top_actions')
    @if($listing->exists)
        <a href="{{ route('admin.listings.show', $listing) }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2.5 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Voir les détails
        </a>
    @endif
@endsection

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <div x-data="adminListingForm()" x-init="init()">
    <form action="{{ $listing->exists ? route('admin.listings.update', $listing) : route('admin.listings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($listing->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Basic Info --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informations générales
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Owner --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Propriétaire <span class="text-red-500">*</span></label>
                            <select name="user_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $listing->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Titre de l'annonce <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $listing->title) }}" required maxlength="255"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                   placeholder="Ex: Villa moderne avec piscine à Marrakech">
                            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" required minlength="20"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold resize-none"
                                      placeholder="Décrivez votre bien en détail...">{{ old('description', $listing->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Transaction Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type de transaction <span class="text-red-500">*</span></label>
                            <select name="transaction_type" x-model="form.transaction_type" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                @foreach($transactionTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('transaction_type', $listing->transaction_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Property Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type de bien <span class="text-red-500">*</span></label>
                            <select name="property_type" x-model="form.property_type" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                @foreach($propertyTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('property_type', $listing->property_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prix (MAD) <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="price" value="{{ old('price', $listing->price) }}" required min="0" step="0.01"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                       placeholder="Ex: 1500000">
                                
                                <template x-if="form.transaction_type === 'location' || form.transaction_type === 'vacances'">
                                    <select name="price_period" x-model="form.price_period"
                                            class="w-32 px-3 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold text-sm">
                                        @foreach($pricePeriods ?? \App\Models\Listing::PRICE_PERIODS as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </template>
                            </div>
                        </div>

                        {{-- Surface --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Surface (m²)</label>
                            <input type="number" name="surface" value="{{ old('surface', $listing->surface) }}" min="0" step="0.01"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                   placeholder="Ex: 150">
                        </div>
                    </div>
                </div>

                {{-- Location --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Localisation
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- City --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ville <span class="text-red-500">*</span></label>
                            <select name="city" x-model="form.city" @change="updateMapCity()" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                <option value="">Sélectionner une ville</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ old('city', $listing->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Zone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quartier / Zone</label>
                            <select name="zone" x-model="form.zone" :disabled="!availableZones.length"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold disabled:opacity-50">
                                <option value="">Choisir un quartier</option>
                                <template x-for="z in availableZones" :key="z">
                                    <option :value="z" x-text="z" :selected="form.zone === z"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Adresse complète</label>
                            <div class="flex gap-2">
                                <input type="text" name="address" x-model="form.address"
                                       class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                       placeholder="Ex: 123 Avenue Mohammed V">
                                <button type="button" @click="geocodeAddress()"
                                        :disabled="geoLoading || !form.city"
                                        class="px-5 py-3 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 rounded-xl text-sm font-medium hover:opacity-90 transition-opacity disabled:opacity-50 whitespace-nowrap shadow-sm flex items-center gap-2">
                                    <span x-show="!geoLoading">Localiser</span>
                                    <span x-show="geoLoading">...</span>
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="latitude" x-model="form.lat">
                        <input type="hidden" name="longitude" x-model="form.lng">

                        {{-- Map --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 flex justify-between">
                                <span>Position sur la carte</span>
                                <span class="text-xs text-gray-400 font-normal">Cliquez sur la carte pour ajuster</span>
                            </label>
                            <div id="createMap" class="h-[280px] rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-10 relative"></div>
                            <div x-show="geoMessage" class="mt-2 text-sm text-sky-600 dark:text-sky-400" x-text="geoMessage"></div>
                        </div>
                    </div>
                </div>

                {{-- Features --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Caractéristiques du bien
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        {{-- Pièces --}}
                        <div x-show="['appartement','villa','riad','bureau','local'].includes(form.property_type)">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pièces</label>
                            <input type="number" name="rooms" value="{{ old('rooms', $listing->rooms) }}" min="0"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- Salles de bain (Appartement, Villa, Riad) --}}
                        <div x-show="['appartement','villa','riad'].includes(form.property_type)">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Salles de bain</label>
                            <input type="number" name="bathrooms" value="{{ old('bathrooms', $listing->bathrooms) }}" min="0"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- Étage (Appartement, Bureau, Local) --}}
                        <div x-show="['appartement','bureau','local','entrepot'].includes(form.property_type)">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Étage</label>
                            <input type="number" name="floor" value="{{ old('floor', $listing->floor) }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- État du bien (Appartement) --}}
                        <div x-show="form.property_type === 'appartement'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">État du bien</label>
                            <select name="condition" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                <option value="">—</option>
                                <option value="neuf" {{ old('condition', $listing->condition ?? '') === 'neuf' ? 'selected' : '' }}>Neuf</option>
                                <option value="excellent" {{ old('condition', $listing->condition ?? '') === 'excellent' ? 'selected' : '' }}>Excellent état</option>
                                <option value="bon" {{ old('condition', $listing->condition ?? '') === 'bon' ? 'selected' : '' }}>Bon état</option>
                                <option value="a_renover" {{ old('condition', $listing->condition ?? '') === 'a_renover' ? 'selected' : '' }}>À rénover</option>
                            </select>
                        </div>

                        {{-- Surface terrain (Villa) --}}
                        <div x-show="form.property_type === 'villa'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Surface terrain (m²)</label>
                            <input type="number" name="land_surface" value="{{ old('land_surface', $listing->land_surface ?? '') }}" 
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- Nature du terrain (Terrain) --}}
                        <div x-show="form.property_type === 'terrain'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nature du terrain</label>
                            <select name="land_type" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                <option value="">—</option>
                                <option value="constructible" {{ old('land_type', $listing->land_type ?? '') === 'constructible' ? 'selected' : '' }}>Constructible</option>
                                <option value="agricole" {{ old('land_type', $listing->land_type ?? '') === 'agricole' ? 'selected' : '' }}>Agricole</option>
                                <option value="industriel" {{ old('land_type', $listing->land_type ?? '') === 'industriel' ? 'selected' : '' }}>Industriel</option>
                                <option value="autre" {{ old('land_type', $listing->land_type ?? '') === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        
                        {{-- Façade (Terrain) --}}
                        <div x-show="form.property_type === 'terrain'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Façade (ml)</label>
                            <input type="number" name="facade" value="{{ old('facade', $listing->facade ?? '') }}" 
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>
                        
                        {{-- COS (Terrain) --}}
                        <div x-show="form.property_type === 'terrain'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">COS / CUS</label>
                            <input type="text" name="cos" value="{{ old('cos', $listing->cos ?? '') }}" 
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- Nombre de patios (Riad) --}}
                        <div x-show="form.property_type === 'riad'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre de patios</label>
                            <input type="number" name="patios" value="{{ old('patios', $listing->patios ?? '') }}" 
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- Usage Riad --}}
                        <div x-show="form.property_type === 'riad'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Usage</label>
                            <select name="riad_usage" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                <option value="">—</option>
                                <option value="habitation" {{ old('riad_usage', $listing->riad_usage ?? '') === 'habitation' ? 'selected' : '' }}>Habitation</option>
                                <option value="maison_hotes" {{ old('riad_usage', $listing->riad_usage ?? '') === 'maison_hotes' ? 'selected' : '' }}>Maison d'hôtes</option>
                                <option value="investissement" {{ old('riad_usage', $listing->riad_usage ?? '') === 'investissement' ? 'selected' : '' }}>Investissement</option>
                            </select>
                        </div>

                        {{-- Hauteur sous plafond (Bureau/Local) --}}
                        <div x-show="['bureau','local','entrepot'].includes(form.property_type)">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Hauteur sous plafond (m)</label>
                            <input type="number" step="0.1" name="ceiling_height" value="{{ old('ceiling_height', $listing->ceiling_height ?? '') }}" 
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>

                        {{-- Type de local --}}
                        <div x-show="['bureau','local','entrepot'].includes(form.property_type)">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type de local</label>
                            <select name="commercial_type" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                <option value="">—</option>
                                <option value="open_space" {{ old('commercial_type', $listing->commercial_type ?? '') === 'open_space' ? 'selected' : '' }}>Open space</option>
                                <option value="cloisonne" {{ old('commercial_type', $listing->commercial_type ?? '') === 'cloisonne' ? 'selected' : '' }}>Cloisonné</option>
                                <option value="mixte" {{ old('commercial_type', $listing->commercial_type ?? '') === 'mixte' ? 'selected' : '' }}>Mixte</option>
                            </select>
                        </div>
                    </div>

                    {{-- Amenities --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Équipements</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $amenities = [
                                    'furnished' => ['Meublé', 'M3 7a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'],
                                    'parking' => ['Parking', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                    'elevator' => ['Ascenseur', 'M8 7l4-4m0 0l4 4m-4-4v18'],
                                    'pool' => ['Piscine', 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                                    'garden' => ['Jardin', 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                                    'terrace' => ['Terrasse', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                    'security' => ['Sécurité 24/7', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                                ];
                            @endphp
                            @foreach($amenities as $field => [$label, $icon])
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-gold/40 transition
                                             {{ old($field, $listing->$field) ? 'bg-gold/5 border-gold/40' : 'bg-white dark:bg-gray-800' }}">
                                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $listing->$field) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-gold focus:ring-gold">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Photos --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Photos
                    </h3>

                    @if($listing->exists && $listing->images->isNotEmpty())
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            @foreach($listing->images as $img)
                                <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 group">
                                    <img src="{{ $img->url }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-medium">Connecté</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer hover:border-gold/50 hover:bg-gold/5 transition-all text-center px-4">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cliquez ou déposez vos nouvelles photos</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">JPG, PNG, WebP · Aucune limite de nombre</span>
                        <input type="file" name="images[]" multiple accept="image/*" class="sr-only" @change="previewImages($event)">
                    </label>

                    <div x-show="previews.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        <template x-for="(src, i) in previews" :key="i">
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                                <img :src="src" class="w-full h-full object-cover">
                                <button type="button" @click="removePreview(i)"
                                        class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-sm flex items-center justify-center hover:bg-red-600 shadow-sm transition-colors transform hover:scale-110">
                                    ×
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Statut & Visibilité
                    </h3>

                    <div class="space-y-4">
                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Statut <span class="text-red-500">*</span></label>
                            <select name="status" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $listing->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Featured --}}
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-gold/40 transition
                                     {{ old('featured', $listing->featured) ? 'bg-gold/5 border-gold/40' : 'bg-white dark:bg-gray-800' }}">
                            <input type="checkbox" name="featured" value="1" {{ old('featured', $listing->featured) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-gold focus:ring-gold">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Mettre en vedette</div>
                                <div class="text-xs text-gray-500">L'annonce sera mise en avant sur la page d'accueil</div>
                            </div>
                        </label>

                        {{-- Priority --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priorité d'affichage</label>
                            <div class="flex items-center gap-4">
                                <input type="range" name="priority" value="{{ old('priority', $listing->priority ?? 0) }}" min="0" max="10"
                                       class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-gold"
                                       id="priority-slider">
                                <span id="priority-value" class="text-lg font-bold {{ ($listing->priority ?? 0) > 5 ? 'text-orange-500' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ old('priority', $listing->priority ?? 0) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <span id="priority-label">{{ $listing->priority_label ?? 'Normale' }}</span> — 
                                Les annonces avec une priorité élevée apparaissent en premier dans les résultats de recherche.
                            </p>
                            <script>
                                document.getElementById('priority-slider').addEventListener('input', function(e) {
                                    const value = parseInt(e.target.value);
                                    document.getElementById('priority-value').textContent = value;
                                    let label = 'Normale';
                                    let color = 'text-gray-700 dark:text-gray-300';
                                    if (value >= 8) { label = 'Priorité maximale'; color = 'text-red-500'; }
                                    else if (value >= 5) { label = 'Priorité haute'; color = 'text-orange-500'; }
                                    else if (value >= 3) { label = 'Priorité moyenne'; color = 'text-yellow-600'; }
                                    else if (value >= 1) { label = 'Priorité basse'; color = 'text-blue-500'; }
                                    document.getElementById('priority-label').textContent = label;
                                    document.getElementById('priority-value').className = 'text-lg font-bold ' + color;
                                });
                            </script>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="panel rounded-2xl p-6">
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full bg-gold hover:bg-gold-dark text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-gold/30">
                            {{ $listing->exists ? 'Enregistrer les modifications' : 'Créer l\'annonce' }}
                        </button>
                        <a href="{{ route('admin.listings.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-3 rounded-xl transition">
                            Annuler
                        </a>
                    </div>
                </div>

                {{-- (Les images actuelles sont maintenant dans la colonne principale) --}}

                {{-- Info --}}
                @if($listing->exists)
                    <div class="panel rounded-2xl p-5 text-xs text-gray-500 space-y-3">
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8h2m6 0h2m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" /></svg>
                            Réf: DM-{{ str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Créée: {{ $listing->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Modifiée: {{ $listing->updated_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Vues: {{ number_format($listing->views) }}
                        </p>
                        <p class="flex items-center gap-2 text-red-500 font-medium">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            Favoris: {{ $listing->favorites()->count() }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </form>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
function adminListingForm() {
    return {
        form: {
            transaction_type: '{{ old("transaction_type", $listing->transaction_type ?? "") }}',
            property_type: '{{ old("property_type", $listing->property_type ?? "") }}',
            price_period: '{{ old("price_period", $listing->price_period ?? "mois") }}',
            city: '{{ old("city", $listing->city ?? "") }}',
            zone: '{{ old("zone", $listing->zone ?? "") }}',
            address: '{{ old("address", $listing->address ?? "") }}',
            lat: '{{ old("latitude", $listing->latitude ?? "") }}',
            lng: '{{ old("longitude", $listing->longitude ?? "") }}',
        },
        geoLoading: false,
        geoMessage: '',
        previews: [],
        map: null,
        marker: null,

        cityCoords: {
            'Casablanca': [33.5731, -7.5898], 'Rabat': [34.0209, -6.8416],
            'Marrakech':  [31.6295, -7.9811], 'Fès':   [34.0181, -5.0078],
            'Tanger':     [35.7595, -5.8340], 'Agadir':[30.4278, -9.5981],
            'Meknès':     [33.8931, -5.5473], 'Oujda': [34.6814, -1.9086],
        },

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
                attribution: '© OpenStreetMap contributors',
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
            this.form.zone = '';
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
                const query = `${this.form.address ? this.form.address + ', ' : ''}${this.form.zone ? this.form.zone + ', ' : ''}${this.form.city}, Maroc`;
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;
                const res = await fetch(url);
                const data = await res.json();
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    this.placeMarker(lat, lng);
                    this.map.setView([lat, lng], 16);
                    this.geoMessage = "Position trouvée.";
                } else {
                    this.geoMessage = "Adresse introuvable. Veuillez placer le marqueur manuellement.";
                }
            } catch(e) {
                this.geoMessage = "Erreur lors de la recherche. Placez le marqueur manuellement.";
            } finally {
                this.geoLoading = false;
            }
        },

        previewImages(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        removePreview(i) {
            this.previews.splice(i, 1);
        },
    };
}
</script>
@endsection
