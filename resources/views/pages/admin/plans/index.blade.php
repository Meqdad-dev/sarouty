@extends('layouts.admin')

@section('title', 'Gestion des Abonnements')
@section('page_title', 'Gestion des Abonnements')

@section('top_actions')
<a href="{{ route('admin.plans.create') }}" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-gold/30 hover:shadow-gold/50">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Ajouter un plan
</a>
@endsection

@section('content')
    {{-- Shared Tab Navigation --}}
    <div class="panel rounded-2xl p-2 mb-8">
        <nav class="flex flex-wrap gap-1">
            @php $groups = \App\Models\Setting::GROUPS; @endphp
            @foreach($groups as $groupKey => $groupLabel)
                <a href="{{ route('admin.settings.index', ['group' => $groupKey]) }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                    {{ $groupLabel }}
                </a>
            @endforeach
            <a href="{{ route('admin.plans.index') }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all bg-gold text-white shadow-lg shadow-gold/30">
                Abonnements
            </a>
        </nav>
    </div>

    {{-- Plan Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        @foreach($plans as $plan)
            @php $preset = $plan->theme_preset; @endphp
            <div class="relative rounded-2xl overflow-hidden flex flex-col shadow-sm border"
                 style="border-color: {{ $preset['border'] }}; background: {{ $preset['soft'] }};">

                {{-- Color accent header --}}
                <div class="h-2 w-full" style="background: {{ $preset['hex'] }};"></div>

                <div class="p-5 flex flex-col flex-1 gap-4">
                    {{-- Badge + Name --}}
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center gap-2 mb-1">
                                <span class="w-3 h-3 rounded-full inline-block" style="background: {{ $preset['hex'] }};"></span>
                                <span class="text-xs font-semibold uppercase tracking-wider" style="color: {{ $preset['text'] }};">{{ $plan->theme_name }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $plan->slug }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($plan->is_active)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Actif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactif</span>
                            @endif
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="py-3 px-4 rounded-xl text-center" style="background: {{ $preset['hex'] }}15; border: 1px solid {{ $preset['border'] }};">
                        @if($plan->price == 0)
                            <span class="text-3xl font-bold" style="color: {{ $preset['text'] }};">Gratuit</span>
                        @else
                            <span class="text-3xl font-bold" style="color: {{ $preset['text'] }};">{{ number_format($plan->price, 0, ',', ' ') }} MAD</span>
                            @if($plan->duration_days)
                                <span class="text-xs text-gray-500 block mt-0.5">/ {{ $plan->duration_days }} jours</span>
                            @endif
                        @endif
                    </div>

                    {{-- Quotas --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Annonces (part. / agent)</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $plan->max_ads_particulier }} / {{ $plan->max_ads_agent }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Photos max.</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $plan->max_images_particulier }} / {{ $plan->max_images_agent }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Priorité</span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background: {{ $preset['hex'] }};">{{ $plan->priority_level }}</span>
                        </div>
                        @if($plan->can_create_sponsored_listing)
                            <div class="flex items-center gap-2 mt-1 pt-2 border-t" style="border-color: {{ $preset['border'] }};">
                                <svg class="w-4 h-4" style="color: {{ $preset['hex'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-xs text-gray-600">Sponsorisations offertes ({{ $plan->sponsored_listing_duration_days }}j)</span>
                            </div>
                        @endif
                    </div>

                    {{-- Features Preview --}}
                    @if($plan->features && count($plan->features) > 0)
                        <ul class="space-y-1.5 flex-1">
                            @foreach(array_slice($plan->features, 0, 4) as $feature)
                                <li class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" style="color: {{ $preset['hex'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                            @if(count($plan->features) > 4)
                                <li class="text-xs text-gray-400 pl-5">+ {{ count($plan->features) - 4 }} autres avantages</li>
                            @endif
                        </ul>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-3 border-t" style="border-color: {{ $preset['border'] }};">
                        <a href="{{ route('admin.plans.edit', $plan) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold transition-all text-white hover:opacity-90"
                           style="background: {{ $preset['button'] }}; box-shadow: 0 12px 24px {{ $preset['glow'] }};">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Modifier
                        </a>
                        @if($plan->slug !== 'gratuit')
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST"
                                  onsubmit="return confirm('Supprimer ce plan ?');" class="inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table Summary --}}
    <div class="panel rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <h3 class="font-semibold text-gray-900 dark:text-white">Tableau récapitulatif</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-gray-500 bg-gray-50 dark:bg-gray-800/50">
                        <th class="px-6 py-3 font-medium">Couleur</th>
                        <th class="px-4 py-3 font-medium">Nom / Slug</th>
                        <th class="px-4 py-3 font-medium text-center">Niveau</th>
                        <th class="px-4 py-3 font-medium">Prix (MAD)</th>
                        <th class="px-4 py-3 font-medium">Annonces (Part/Pro)</th>
                        <th class="px-4 py-3 font-medium">Photos max</th>
                        <th class="px-4 py-3 font-medium text-center">Statut</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($plans as $plan)
                        @php $preset = $plan->theme_preset; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full flex-shrink-0 ring-2 ring-white dark:ring-gray-800 shadow" style="background: {{ $preset['hex'] }};"></span>
                                    <span class="text-xs text-gray-500">{{ $plan->theme_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $plan->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $plan->slug }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shadow-sm" style="background: {{ $preset['hex'] }};">{{ $plan->priority_level }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold" style="color: {{ $preset['hex'] }};">
                                {{ $plan->price == 0 ? 'Gratuit' : number_format($plan->price, 0, ',', ' ') }}
                                @if($plan->duration_days)
                                    <span class="text-xs font-normal text-gray-500"> / {{ $plan->duration_days }}j</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $plan->max_ads_particulier }} / {{ $plan->max_ads_agent }}</td>
                            <td class="px-4 py-3">{{ $plan->max_images_particulier }} / {{ $plan->max_images_agent }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($plan->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 rounded-lg text-xs font-medium">Actif</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400 rounded-lg text-xs font-medium">Inactif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.plans.edit', $plan) }}" class="p-2 text-gray-400 hover:text-blue-500 transition" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    @if($plan->slug !== 'gratuit')
                                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST"
                                              onsubmit="return confirm('Supprimer ce plan ?');" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
