@extends('layouts.admin')
@php cache()->put('admin_viewed_listings_' . auth()->id(), now()); @endphp

@section('title', 'Gestion des Annonces – Sarouty')
@section('page_title', 'Gestion des Annonces')
@section('page_subtitle', 'Modérer et gérer toutes les annonces de la plateforme')

@section('top_actions')
    <a href="{{ route('admin.listings.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle annonce
    </a>
@endsection

@section('content')
    {{-- Filters --}}
    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.listings.index') }}" class="flex flex-wrap items-end gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Rechercher</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Titre, ville, zone..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Statut</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- City Filter --}}
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Ville</label>
                <select name="city" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Toutes les villes</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Transaction Type Filter --}}
            <div class="w-44">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Type de transaction</label>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous les types</option>
                    @foreach($transactionTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.listings.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Stats Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php
            $statusCounts = [
                'total' => $listings->total(),
                'pending' => \App\Models\Listing::where('status', 'pending')->count(),
                'active' => \App\Models\Listing::where('status', 'active')->count(),
                'rejected' => \App\Models\Listing::where('status', 'rejected')->count(),
                'featured' => \App\Models\Listing::where('featured', true)->where('status', 'active')->count(),
            ];
        @endphp
        <div class="panel rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statusCounts['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'pending' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-2xl font-bold text-amber-600">{{ number_format($statusCounts['pending']) }}</div>
            <div class="text-xs text-gray-500">En attente</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'active' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($statusCounts['active']) }}</div>
            <div class="text-xs text-gray-500">Actives</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'rejected' ? 'ring-2 ring-red-400' : '' }}">
            <div class="text-2xl font-bold text-red-600">{{ number_format($statusCounts['rejected']) }}</div>
            <div class="text-xs text-gray-500">Refusées</div>
        </div>
        <div class="panel rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-gold">{{ number_format($statusCounts['featured']) }}</div>
            <div class="text-xs text-gray-500">Vedettes</div>
        </div>
    </div>

    {{-- Listings Table --}}
    <div class="panel rounded-2xl overflow-hidden">
        @if($listings->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucune annonce trouvée</h3>
                <p class="text-sm text-gray-500">Essayez de modifier vos filtres ou créez une nouvelle annonce.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Annonce</th>
                            <th class="px-4 py-4">Ville</th>
                            <th class="px-4 py-4">Type</th>
                            <th class="px-4 py-4">Prix</th>
                            <th class="px-4 py-4">Statut</th>
                            <th class="px-4 py-4">Auteur</th>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($listings as $listing)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                {{-- Annonce Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                            @if($listing->images->first())
                                                <img src="{{ $listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $listing->title }}</div>
                                            <div class="text-xs text-gray-400">ID: DM-{{ str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</div>
                                            @if($listing->is_sponsored)
                                                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                                    ⭐ Sponsorisée
                                                    @if(($listing->sponsorship?->amount ?? 0) == 0)
                                                        <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Abonnement</span>
                                                    @endif
                                                </span>
                                            @endif
                                            @if($listing->featured)
                                                <span class="inline-flex items-center gap-1 text-xs text-gold font-medium">
                                                    ⭐ Vedette
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Ville --}}
                                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $listing->city }}</td>

                                {{-- Type --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ $listing->transaction_label }}
                                    </span>
                                </td>

                                {{-- Prix --}}
                                <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white">{{ $listing->formatted_price }}</td>

                                {{-- Statut --}}
                                <td class="px-4 py-4">
                                    <div x-data="{ status: '{{ $listing->status }}', open: false }" class="relative">
                                        <button @click="open = !open"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition
                                                @if($listing->status === 'active') bg-emerald-100 text-emerald-700
                                                @elseif($listing->status === 'pending') bg-amber-100 text-amber-700
                                                @elseif($listing->status === 'rejected') bg-red-100 text-red-700
                                                @elseif($listing->status === 'sold') bg-blue-100 text-blue-700
                                                @elseif($listing->status === 'rented') bg-purple-100 text-purple-700
                                                @else bg-gray-100 text-gray-600 @endif">
                                            <span class="w-1.5 h-1.5 rounded-full
                                                @if($listing->status === 'active') bg-emerald-500
                                                @elseif($listing->status === 'pending') bg-amber-500
                                                @elseif($listing->status === 'rejected') bg-red-500
                                                @else bg-gray-400 @endif"></span>
                                            {{ $listing->status_label }}
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>

                                        <div x-show="open" @click.away="open = false" x-transition
                                             class="absolute z-20 top-full left-0 mt-1 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1 overflow-hidden">
                                            @foreach(['pending', 'active', 'rejected', 'sold', 'rented'] as $newStatus)
                                                @if($newStatus !== $listing->status)
                                                    <form action="{{ route('admin.listings.update-status', $listing) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="{{ $newStatus }}">
                                                        <button type="submit" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center gap-2">
                                                            <span class="w-2 h-2 rounded-full
                                                                @if($newStatus === 'active') bg-emerald-500
                                                                @elseif($newStatus === 'pending') bg-amber-500
                                                                @elseif($newStatus === 'rejected') bg-red-500
                                                                @elseif($newStatus === 'sold') bg-blue-500
                                                                @elseif($newStatus === 'rented') bg-purple-500
                                                                @else bg-gray-400 @endif"></span>
                                                            {{ \App\Models\Listing::STATUSES[$newStatus] }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </td>

                                {{-- Auteur --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $listing->user->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $listing->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $listing->user->role_label }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-4 text-gray-500 text-xs">
                                    <div>{{ $listing->created_at->format('d/m/Y') }}</div>
                                    <div>{{ $listing->created_at->format('H:i') }}</div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View --}}
                                        <a href="{{ route('admin.listings.show', $listing) }}"
                                           class="nav-ajax p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                           title="Voir les détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.listings.edit', $listing) }}"
                                           class="nav-ajax p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gold transition"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Priority --}}
                                        <div x-data="{ open: false }" class="relative">
                                            <button type="button" @click="open = !open"
                                                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ $listing->priority > 0 ? 'text-orange-500' : 'text-gray-400 hover:text-orange-500' }}"
                                                    title="Priorité: {{ $listing->priority_label }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                                </svg>
                                                @if($listing->priority > 0)
                                                    <span class="absolute -top-1 -right-1 w-4 h-4 text-xs bg-orange-500 text-white rounded-full flex items-center justify-center">{{ $listing->priority }}</span>
                                                @endif
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition
                                                 class="absolute z-20 bottom-full right-0 mb-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-2">
                                                <div class="text-xs font-medium text-gray-500 mb-2 px-2">Définir la priorité</div>
                                                <div class="grid grid-cols-5 gap-1">
                                                    @for($i = 0; $i <= 10; $i++)
                                                        <form action="{{ route('admin.listings.set-priority', $listing) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="priority" value="{{ $i }}">
                                                            <button type="submit"
                                                                    class="w-full py-1.5 text-xs font-medium rounded-lg transition
                                                                        {{ $listing->priority === $i ? 'bg-gold text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gold/20' }}">
                                                                {{ $i }}
                                                            </button>
                                                        </form>
                                                    @endfor
                                                </div>
                                                <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-400 px-2">
                                                    {{ $listing->priority_label }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Feature Toggle --}}
                                        <form action="{{ route('admin.listings.feature', $listing) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ $listing->featured ? 'text-gold' : 'text-gray-400 hover:text-gold' }}"
                                                    title="{{ $listing->featured ? 'Retirer des vedettes' : 'Mettre en avant' }}">
                                                <svg class="w-4 h-4" fill="{{ $listing->featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST"
                                              onsubmit="return confirm('Supprimer définitivement cette annonce ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-600 transition"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
@endsection
