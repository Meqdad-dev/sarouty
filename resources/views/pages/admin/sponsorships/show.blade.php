@extends('layouts.admin')

@section('title', 'Sponsorisation #' . $sponsorship->id . ' – Sarouty')
@section('page_title', 'Détails de la sponsorisation')
@section('page_subtitle', $sponsorship->listing->title ?? 'Annonce supprimée')

@section('top_actions')
    <a href="{{ route('admin.sponsorships.edit', $sponsorship) }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Modifier
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="xl:col-span-2 space-y-6">
            {{-- Status Card --}}
            <div class="panel rounded-2xl p-6">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                        @if($sponsorship->status === 'active') bg-emerald-100 text-emerald-700
                        @elseif($sponsorship->status === 'pending') bg-amber-100 text-amber-700
                        @elseif($sponsorship->status === 'paused') bg-blue-100 text-blue-700
                        @elseif($sponsorship->status === 'expired') bg-gray-100 text-gray-600
                        @else bg-red-100 text-red-700 @endif">
                        <span class="w-2 h-2 rounded-full mr-2
                            @if($sponsorship->status === 'active') bg-emerald-500
                            @elseif($sponsorship->status === 'pending') bg-amber-500
                            @elseif($sponsorship->status === 'paused') bg-blue-500
                            @else bg-gray-400 @endif"></span>
                        {{ $sponsorship->status_label }}
                    </span>
                    
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                        @if($sponsorship->type === 'premium_plus') bg-purple-100 text-purple-700
                        @elseif($sponsorship->type === 'premium') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $sponsorship->type_label }}
                    </span>
                </div>

                {{-- Listing Info --}}
                @if($sponsorship->listing)
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 mb-6">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-200 flex-shrink-0">
                            @if($sponsorship->listing->images->first())
                                <img src="{{ $sponsorship->listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $sponsorship->listing->title }}</h3>
                            <p class="text-sm text-gray-500 mb-2">{{ $sponsorship->listing->city }} • {{ $sponsorship->listing->property_label }}</p>
                            <p class="text-lg font-bold text-gold">{{ number_format($sponsorship->listing->price, 0, ',', ' ') }} MAD</p>
                        </div>
                        <a href="{{ route('admin.listings.show', $sponsorship->listing) }}" class="text-gold hover:text-gold-dark text-sm font-medium">
                            Voir l'annonce →
                        </a>
                    </div>
                @endif

                {{-- Duration Info --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sponsorship->duration_days }}</div>
                        <div class="text-xs text-gray-500">Jours</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sponsorship->remaining_days }}</div>
                        <div class="text-xs text-gray-500">Jours restants</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $sponsorship->starts_at?->format('d/m/Y') ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">Début</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $sponsorship->expires_at?->format('d/m/Y') ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">Expiration</div>
                    </div>
                </div>
            </div>

            {{-- Performance Analytics --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Performance
                </h3>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-5 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10">
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($sponsorship->impressions) }}</div>
                        <div class="text-xs text-blue-600/70 font-medium">Impressions</div>
                    </div>
                    <div class="text-center p-5 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10">
                        <div class="text-3xl font-bold text-emerald-600">{{ number_format($sponsorship->clicks) }}</div>
                        <div class="text-xs text-emerald-600/70 font-medium">Clics</div>
                    </div>
                    <div class="text-center p-5 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-900/10">
                        <div class="text-3xl font-bold text-purple-600">{{ $sponsorship->ctr }}%</div>
                        <div class="text-xs text-purple-600/70 font-medium">CTR</div>
                    </div>
                    <div class="text-center p-5 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-900/10">
                        <div class="text-3xl font-bold text-amber-600">{{ number_format($sponsorship->contacts) }}</div>
                        <div class="text-xs text-amber-600/70 font-medium">Contacts</div>
                    </div>
                </div>

                {{-- Performance Bar --}}
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-500">Taux de clics (CTR)</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $sponsorship->ctr }}%</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-500" style="width: {{ min($sponsorship->ctr * 10, 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-500">Progression durée</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                @php
                                    $progress = $sponsorship->starts_at && $sponsorship->expires_at 
                                        ? min(100, max(0, 100 - ($sponsorship->remaining_days / $sponsorship->duration_days * 100)))
                                        : 0;
                                @endphp
                                {{ round($progress) }}%
                            </span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-gold to-amber-500 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Admin Notes --}}
            @if($sponsorship->admin_notes)
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Notes administrateur</h3>
                    <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $sponsorship->admin_notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Pricing --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tarification</h3>
                <div class="text-center p-6 rounded-xl bg-gradient-to-br from-gold/10 to-amber-50 dark:from-gold/20 dark:to-amber-900/10 border border-gold/20">
                    <div class="text-4xl font-bold text-gold mb-1">{{ $sponsorship->formatted_amount }}</div>
                    <div class="text-sm text-gray-500">Montant payé</div>
                </div>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Type</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $sponsorship->type_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Durée</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $sponsorship->duration_days }} jours</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Prix/jour</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($sponsorship->amount / max(1, $sponsorship->duration_days), 0, ',', ' ') }} MAD</span>
                    </div>
                </div>
            </div>

            {{-- User Info --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-gold/10 flex items-center justify-center">
                        @if($sponsorship->user->avatar)
                            <img src="{{ $sponsorship->user->avatar }}" alt="" class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-gold font-bold">{{ strtoupper(substr($sponsorship->user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $sponsorship->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $sponsorship->user->email }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $sponsorship->user) }}" class="block w-full text-center py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium hover:border-gold/40 hover:text-gold transition">
                    Voir le profil
                </a>
            </div>

            {{-- Payment Info --}}
            @if($sponsorship->payment)
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Paiement</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Statut</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                Confirmé
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ID Transaction</span>
                            <span class="font-mono text-xs text-gray-900 dark:text-white">{{ $sponsorship->payment->stripe_payment_intent ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                <div class="space-y-2">
                    @if($sponsorship->status === 'pending')
                        <form action="{{ route('admin.sponsorships.activate', $sponsorship) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activer maintenant
                            </button>
                        </form>
                    @elseif($sponsorship->status === 'active')
                        <form action="{{ route('admin.sponsorships.pause', $sponsorship) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-blue-500 text-white text-sm font-semibold hover:bg-blue-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mettre en pause
                            </button>
                        </form>
                    @elseif($sponsorship->status === 'paused')
                        <form action="{{ route('admin.sponsorships.resume', $sponsorship) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Reprendre
                            </button>
                        </form>
                    @endif

                    @if(in_array($sponsorship->status, ['pending', 'active', 'paused']))
                        <form action="{{ route('admin.sponsorships.cancel', $sponsorship) }}" method="POST" onsubmit="return confirm('Annuler cette sponsorisation ?')">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Annuler
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.sponsorships.destroy', $sponsorship) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-gray-400 text-sm hover:text-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Historique</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-gold mt-1.5"></div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Création</div>
                            <div class="text-gray-500">{{ $sponsorship->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @if($sponsorship->starts_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Activation</div>
                                <div class="text-gray-500">{{ $sponsorship->starts_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    @if($sponsorship->expires_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-gray-400 mt-1.5"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Expiration prévue</div>
                                <div class="text-gray-500">{{ $sponsorship->expires_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Dernière mise à jour</div>
                            <div class="text-gray-500">{{ $sponsorship->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
