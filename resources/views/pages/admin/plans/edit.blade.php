@extends('layouts.admin')

@section('title', 'Modifier l\'Abonnement')
@section('page_title', 'Modifier: ' . $plan->name)

@section('top_actions')
<a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Retour aux abonnements
</a>
@endsection

@section('content')
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne Principale --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informations Générales --}}
                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Informations Générales
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du forfait *</label>
                            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required {{ $plan->slug === 'gratuit' ? 'readonly' : '' }}
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl focus:ring-gold focus:border-gold px-4 py-2.5 {{ $plan->slug === 'gratuit' ? 'bg-gray-100 dark:bg-gray-900 text-gray-500' : 'bg-white dark:bg-gray-800' }}">
                            @if($plan->slug === 'gratuit')
                                <p class="text-xs text-gray-500 mt-1">Le nom du plan gratuit ne peut pas être modifié.</p>
                            @endif
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (MAD) *</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price) }}" required
                                       class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold pl-4 pr-12 py-2.5">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">MAD</div>
                            </div>
                            @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Période (Jours) <span class="text-xs text-gray-400 font-normal ml-1">Vide = À vie</span></label>
                            <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}"
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-2.5">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fonctionnalités <span class="text-xs text-gray-400 font-normal ml-1">(1 par ligne)</span></label>
                            @php
                                $featuresData = $plan->features;
                                if (is_string($featuresData)) {
                                    $featuresData = json_decode($featuresData, true);
                                }
                                if (!is_array($featuresData)) {
                                    $featuresData = [];
                                }
                            @endphp
                            <textarea name="features" rows="5"
                                      class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-3">{{ old('features', implode("\n", $featuresData)) }}</textarea>
                            @error('features')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Droits & Quotas --}}
                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Droits & Quotas Appliqués
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        {{-- Particuliers --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm uppercase tracking-wider">Pour Profil Particulier</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Quota total d'annonces</label>
                                    <input type="number" name="max_ads_particulier" value="{{ old('max_ads_particulier', $plan->max_ads_particulier) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Nombre d'images par annonce</label>
                                    <input type="number" name="max_images_particulier" value="{{ old('max_images_particulier', $plan->max_images_particulier) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                            </div>
                        </div>

                        {{-- Pro --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm uppercase tracking-wider">Pour Profil Agent / Agence</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Quota total d'annonces</label>
                                    <input type="number" name="max_ads_agent" value="{{ old('max_ads_agent', $plan->max_ads_agent) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Nombre d'images par annonce</label>
                                    <input type="number" name="max_images_agent" value="{{ old('max_images_agent', $plan->max_images_agent) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne Latérale --}}
            <div class="space-y-6">
                {{-- Visibilité des annonces --}}
                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Durée & Affichage</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priorité d'affichage *</label>
                            <input type="number" name="priority_level" value="{{ old('priority_level', $plan->priority_level) }}" required
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold">
                            <p class="text-xs text-gray-500 mt-1">Un chiffre plus élevé place l'annonce plus haut.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durée standard (Jours) *</label>
                            <input type="number" name="listing_duration_days" value="{{ old('listing_duration_days', $plan->listing_duration_days) }}" required
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold">
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durée Sponsorisation (Jours) *</label>
                            <input type="number" name="sponsored_listing_duration_days" value="{{ old('sponsored_listing_duration_days', $plan->sponsored_listing_duration_days) }}" required
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold">
                            
                            <label class="flex items-center gap-2 cursor-pointer mt-3 bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700">
                                <input type="hidden" name="can_create_sponsored_listing" value="0">
                                <input type="checkbox" name="can_create_sponsored_listing" value="1" {{ old('can_create_sponsored_listing', $plan->can_create_sponsored_listing) ? 'checked' : '' }} class="rounded border-gray-300 text-gold focus:ring-gold w-4 h-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-tight">Autoriser les annonces sponsorisées offertes</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Publication</h3>
                    @if($plan->slug !== 'gratuit')
                        <label class="relative inline-flex items-center cursor-pointer mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/20 dark:peer-focus:ring-gold/30 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gold"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Plan actif et public</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Désactivez cette option pour masquer ce forfait.</p>
                    @else
                        <input type="hidden" name="is_active" value="1">
                        <div class="flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg text-sm">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Le plan gratuit est toujours actif.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('admin.plans.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Annuler</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-gold/30 hover:shadow-gold/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
@endsection
