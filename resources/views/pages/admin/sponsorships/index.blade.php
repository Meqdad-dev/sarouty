@extends('layouts.admin')

@section('title', 'Sponsorisations – Sarouty')
@section('page_title', 'Sponsorisations')
@section('page_subtitle', 'Gérez les annonces sponsorisées et suivez leurs performances')

@section('top_actions')
    <a href="{{ route('admin.sponsorships.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle sponsorisation
    </a>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('status') === 'active' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-3xl font-bold text-emerald-600">{{ number_format($stats['active']) }}</div>
            <div class="text-xs text-gray-500">Actives</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('status') === 'pending' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-3xl font-bold text-amber-600">{{ number_format($stats['pending']) }}</div>
            <div class="text-xs text-gray-500">En attente</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('status') === 'expired' ? 'ring-2 ring-gray-400' : '' }}">
            <div class="text-3xl font-bold text-gray-600">{{ number_format($stats['expired']) }}</div>
            <div class="text-xs text-gray-500">Expirées</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gold">{{ number_format($stats['revenue'], 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-500">Revenus (MAD)</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.sponsorships.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Rechercher</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Annonce, utilisateur..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                </div>
            </div>

            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Statut</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    @foreach(\App\Models\Sponsorship::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Type</label>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    @foreach(\App\Models\Sponsorship::TYPES as $value => $data)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $data['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.sponsorships.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Sponsorships Table --}}
    <div class="panel rounded-2xl overflow-hidden">
        @if($sponsorships->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.001 3.001 0 01-1.564-.317z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucune sponsorisation</h3>
                <p class="text-sm text-gray-500">Créez une nouvelle sponsorisation pour commencer.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Annonce</th>
                            <th class="px-4 py-4">Type</th>
                            <th class="px-4 py-4">Statut</th>
                            <th class="px-4 py-4">Durée</th>
                            <th class="px-4 py-4">Prix</th>
                            <th class="px-4 py-4">Performance</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($sponsorships as $sponsorship)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                {{-- Annonce --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                            @if($sponsorship->listing && $sponsorship->listing->images->first())
                                                <img src="{{ $sponsorship->listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white line-clamp-1">
                                                @if($sponsorship->listing?->is_sponsored)⭐ @endif{{ $sponsorship->listing->title ?? 'Annonce supprimée' }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                par {{ $sponsorship->user->name }}
                                            </div>
                                            @if($sponsorship->listing)
                                                <a href="{{ route('admin.listings.show', $sponsorship->listing) }}" class="inline-flex items-center gap-1 mt-1 text-xs text-gold hover:underline">
                                                    Voir l'annonce
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                        @if($sponsorship->type === 'premium_plus') bg-purple-100 text-purple-700
                                        @elseif($sponsorship->type === 'premium') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $sponsorship->type_label }}
                                    </span>
                                </td>

                                {{-- Statut --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                        @if($sponsorship->status === 'active') bg-emerald-100 text-emerald-700
                                        @elseif($sponsorship->status === 'pending') bg-amber-100 text-amber-700
                                        @elseif($sponsorship->status === 'paused') bg-blue-100 text-blue-700
                                        @elseif($sponsorship->status === 'expired') bg-gray-100 text-gray-600
                                        @else bg-red-100 text-red-700 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            @if($sponsorship->status === 'active') bg-emerald-500
                                            @elseif($sponsorship->status === 'pending') bg-amber-500
                                            @elseif($sponsorship->status === 'paused') bg-blue-500
                                            @else bg-gray-400 @endif"></span>
                                        {{ $sponsorship->status_label }}
                                    </span>
                                </td>

                                {{-- Durée --}}
                                <td class="px-4 py-4">
                                    <div class="text-xs">
                                        @if($sponsorship->status === 'active')
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $sponsorship->remaining_days }} jours restants
                                            </div>
                                            <div class="text-gray-400">
                                                Exp: {{ $sponsorship->expires_at->format('d/m/Y') }}
                                            </div>
                                        @elseif($sponsorship->starts_at)
                                            <div>{{ $sponsorship->duration_days }} jours</div>
                                            <div class="text-gray-400">{{ $sponsorship->starts_at->format('d/m/Y') }}</div>
                                        @else
                                            <div>{{ $sponsorship->duration_days }} jours</div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Prix --}}
                                <td class="px-4 py-4">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $sponsorship->formatted_amount }}</span>
                                </td>

                                {{-- Performance --}}
                                <td class="px-4 py-4">
                                    <div class="text-xs space-y-1">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            <span class="font-medium">{{ number_format($sponsorship->impressions) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5" /></svg>
                                            <span class="font-medium">{{ number_format($sponsorship->clicks) }}</span>
                                            @if($sponsorship->impressions > 0)
                                                <span class="text-gold">({{ $sponsorship->ctr }}%)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View --}}
                                        <a href="{{ route('admin.sponsorships.show', $sponsorship) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                           title="Voir les détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gold transition"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        @if($sponsorship->status === 'pending')
                                            <form action="{{ route('admin.sponsorships.activate', $sponsorship) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="p-2 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-gray-400 hover:text-emerald-600 transition"
                                                        title="Activer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif($sponsorship->status === 'active')
                                            <form action="{{ route('admin.sponsorships.pause', $sponsorship) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-400 hover:text-blue-600 transition"
                                                        title="Mettre en pause">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif($sponsorship->status === 'paused')
                                            <form action="{{ route('admin.sponsorships.resume', $sponsorship) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="p-2 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-gray-400 hover:text-emerald-600 transition"
                                                        title="Reprendre">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Cancel --}}
                                        @if(in_array($sponsorship->status, ['pending', 'active', 'paused']))
                                            <form action="{{ route('admin.sponsorships.cancel', $sponsorship) }}" method="POST"
                                                  onsubmit="return confirm('Annuler cette sponsorisation ?')">
                                                @csrf
                                                <button type="submit"
                                                        class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-600 transition"
                                                        title="Annuler">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
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

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $sponsorships->links() }}
            </div>
        @endif
    </div>
@endsection
