@extends('layouts.admin')

@section('title', 'Ajouter un Abonnement')
@section('page_title', 'Ajouter un Abonnement')
@section('page_subtitle', 'Créez un nouveau forfait avec un style visuel cohérent et une couleur dédiée')

@section('top_actions')
<a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Retour aux abonnements
</a>
@endsection

@section('content')
    <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-6"
          x-data='planDesigner(@json($themePresets), @json(old("theme_color", "gold")))'>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="panel p-6 rounded-2xl">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white shadow-lg"
                             :style="`background:${activeTheme().hex}`">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informations générales</h3>
                            <p class="text-sm text-gray-500">Titre, prix, durée et avantages affichés sur la page tarifs.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du forfait *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Starter, Pro..."
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-2.5">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix (MAD) *</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="price" value="{{ old('price', 0) }}" required
                                       class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold pl-4 pr-12 py-2.5">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">MAD</div>
                            </div>
                            @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Période (jours)</label>
                            <input type="number" name="duration_days" value="{{ old('duration_days') }}" placeholder="Ex: 30"
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-2.5">
                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour afficher le plan comme illimité / gratuit.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fonctionnalités</label>
                            <textarea name="features" rows="5" placeholder="Support dédié&#10;Visibilité premium&#10;Statistiques avancées..."
                                      class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-3">{{ old('features') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Une fonctionnalité par ligne pour l'affichage dans la carte tarif.</p>
                            @error('features')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="panel p-6 rounded-2xl">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-2xl bg-gold/10 text-gold flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Droits & quotas</h3>
                            <p class="text-sm text-gray-500">Paramètres appliqués automatiquement aux comptes liés à ce forfait.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm uppercase tracking-wider">Profil particulier</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Quota total d'annonces</label>
                                    <input type="number" name="max_ads_particulier" value="{{ old('max_ads_particulier', 2) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Images par annonce</label>
                                    <input type="number" name="max_images_particulier" value="{{ old('max_images_particulier', 5) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm uppercase tracking-wider">Profil agent / agence</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Quota total d'annonces</label>
                                    <input type="number" name="max_ads_agent" value="{{ old('max_ads_agent', 5) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Images par annonce</label>
                                    <input type="number" name="max_images_agent" value="{{ old('max_images_agent', 10) }}" required
                                           class="w-full border-gray-200 dark:border-gray-700 rounded-lg dark:bg-gray-800 focus:ring-gold focus:border-gold px-3 py-2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Couleur du forfait</h3>
                    <input type="hidden" name="theme_color" x-model="selected">
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($themePresets as $key => $theme)
                            <label class="rounded-2xl border p-3 cursor-pointer transition-all"
                                   :class="selected === '{{ $key }}' ? 'ring-2 ring-offset-2 ring-gold border-transparent shadow-lg' : 'border-gray-200 dark:border-gray-700 hover:border-gold/40'"
                                   @click="selected = '{{ $key }}'">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-2xl shadow-sm" style="background: {{ $theme['hex'] }};"></span>
                                    <div>
                                        <div class="font-semibold text-sm text-gray-900 dark:text-white">{{ $theme['name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $theme['hex'] }}</div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('theme_color')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Durée & affichage</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priorité d'affichage *</label>
                            <input type="number" name="priority_level" value="{{ old('priority_level', 0) }}" required
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durée standard (jours) *</label>
                            <input type="number" name="listing_duration_days" value="{{ old('listing_duration_days', 30) }}" required
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-2.5">
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durée sponsorisation (jours) *</label>
                            <input type="number" name="sponsored_listing_duration_days" value="{{ old('sponsored_listing_duration_days', 0) }}" required
                                   class="w-full border-gray-200 dark:border-gray-700 rounded-xl dark:bg-gray-800 focus:ring-gold focus:border-gold px-4 py-2.5">

                            <label class="flex items-center gap-2 cursor-pointer mt-3 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <input type="hidden" name="can_create_sponsored_listing" value="0">
                                <input type="checkbox" name="can_create_sponsored_listing" value="1" {{ old('can_create_sponsored_listing') ? 'checked' : '' }} class="rounded border-gray-300 text-gold focus:ring-gold w-4 h-4">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-tight">Autoriser les annonces sponsorisées offertes</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Aperçu rapide</h3>
                    <div class="rounded-[26px] overflow-hidden border shadow-sm"
                         :style="`border-color:${activeTheme().border}; box-shadow: 0 20px 50px ${activeTheme().glow}; background:${activeTheme().soft};`">
                        <div class="h-2 w-full" :style="`background:${activeTheme().hex}`"></div>
                        <div class="p-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-4"
                                 :style="`background:${activeTheme().hex}18; color:${activeTheme().text};`">
                                <span class="w-2.5 h-2.5 rounded-full" :style="`background:${activeTheme().hex}`"></span>
                                <span x-text="activeTheme().name"></span>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white" x-text="$el.closest('form').querySelector('[name=name]').value || 'Nom du forfait'"></h4>
                            <p class="mt-3 text-3xl font-bold" :style="`color:${activeTheme().text}`" x-text="priceLabel($el.closest('form').querySelector('[name=price]').value)"></p>
                            <div class="mt-4 text-sm text-gray-600 space-y-1">
                                <div>• Mise en avant visuelle dans l'admin</div>
                                <div>• Badge de couleur dans les listes</div>
                                <div>• Style synchronisé avec la page tarifs</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel p-6 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Publication</h3>
                    <label class="relative inline-flex items-center cursor-pointer mb-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/20 dark:peer-focus:ring-gold/30 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gold"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Plan actif et visible</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-2">Décochez pour préparer ce forfait sans l'afficher dans les tarifs.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('admin.plans.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Annuler</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-gold/30 hover:shadow-gold/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Créer l'abonnement
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function planDesigner(themes, initial) {
        return {
            themes,
            selected: initial || 'gold',
            activeTheme() {
                return this.themes[this.selected] || Object.values(this.themes)[0];
            },
            priceLabel(value) {
                const amount = parseFloat(value || 0);
                if (!amount) return 'Gratuit';
                return new Intl.NumberFormat('fr-FR').format(amount) + ' MAD';
            }
        }
    }
</script>
@endpush
