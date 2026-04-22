@extends('layouts.admin')

@section('title', 'Abonnement #' . $payment->id . ' – Sarouty')
@section('page_title', 'Détails de l\'abonnement')
@section('page_subtitle', $payment->plan_label . ' - ' . ($payment->user->name ?? 'Utilisateur'))

@section('top_actions')
    <a href="{{ route('admin.subscriptions.edit', $payment) }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
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
                        @if($payment->plan === 'agence') bg-purple-100 text-purple-700
                        @elseif($payment->plan === 'pro') bg-blue-100 text-blue-700
                        @elseif($payment->plan === 'starter') bg-emerald-100 text-emerald-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $payment->plan_label }}
                    </span>
                    
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                        @if($payment->status === 'completed') bg-emerald-100 text-emerald-700
                        @elseif($payment->status === 'pending') bg-amber-100 text-amber-700
                        @elseif($payment->status === 'failed') bg-red-100 text-red-700
                        @else bg-purple-100 text-purple-700 @endif">
                        <span class="w-1.5 h-1.5 rounded-full
                            @if($payment->status === 'completed') bg-emerald-500
                            @elseif($payment->status === 'pending') bg-amber-500
                            @elseif($payment->status === 'failed') bg-red-500
                            @else bg-purple-500 @endif"></span>
                        {{ $payment->status_label }}
                    </span>

                    @if($payment->is_active)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            ✓ Abonnement actif
                        </span>
                    @endif
                </div>

                {{-- Duration Info --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $payment->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">Date d'achat</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $payment->remaining_days }}</div>
                        <div class="text-xs text-gray-500">Jours restants</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $payment->expires_at?->format('d/m/Y') ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">Expiration</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gold/10 border border-gold/20">
                        <div class="text-2xl font-bold text-gold">{{ $payment->formatted_amount }}</div>
                        <div class="text-xs text-gray-500">Montant</div>
                    </div>
                </div>
            </div>

            {{-- Plan Features --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Avantages du plan {{ $payment->plan_label }}</h3>
                <ul class="space-y-2">
                    @foreach(\App\Models\User::PLANS[$payment->plan]['features'] ?? [] as $feature)
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <polyline points="20,6 9,17 4,12" stroke-width="2.5"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- User Listings --}}
            @if($payment->user && $payment->user->listings->count() > 0)
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Annonces récentes</h3>
                    <div class="space-y-3">
                        @foreach($payment->user->listings->take(5) as $listing)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                                    @if($listing->images->first())
                                        <img src="{{ $listing->images->first()->url }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a2 2 0 01-2 2h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-white truncate">{{ $listing->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $listing->city }} • {{ number_format($listing->price, 0, ',', ' ') }} MAD</div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold
                                    @if($listing->status === 'active') bg-emerald-100 text-emerald-700
                                    @elseif($listing->status === 'pending') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ \App\Models\Listing::STATUSES[$listing->status] ?? $listing->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- User Info --}}
            @if($payment->user)
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Abonné</h3>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center">
                            @if($payment->user->avatar)
                                <img src="{{ $payment->user->avatar_url }}" class="w-full h-full rounded-full object-cover">
                            @else
                                <span class="text-gold font-bold text-lg">{{ strtoupper(substr($payment->user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $payment->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Plan actuel</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->user->plan_label }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Quota</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->user->listings_quota }} annonces</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Annonces</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->user->listings->count() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $payment->user) }}" class="block w-full text-center py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium hover:border-gold/40 hover:text-gold transition">
                        Voir le profil
                    </a>
                </div>
            @endif

            {{-- Payment Info --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Paiement</h3>
                <div class="space-y-3 text-sm">
                    @if($payment->stripe_session_id)
                        <div>
                            <div class="text-gray-500 mb-1">Session Stripe</div>
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded block overflow-x-auto">{{ $payment->stripe_session_id }}</code>
                        </div>
                    @endif
                    @if($payment->stripe_payment_intent)
                        <div>
                            <div class="text-gray-500 mb-1">Payment Intent</div>
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded block overflow-x-auto">{{ $payment->stripe_payment_intent }}</code>
                        </div>
                    @endif
                    @if(!$payment->stripe_session_id && !$payment->stripe_payment_intent)
                        <div class="text-gray-400 text-center py-4">
                            <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Créé par l'admin
                        </div>
                    @endif
                </div>
            </div>

            {{-- Extend Subscription --}}
            @if($payment->status === 'completed')
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Prolonger</h3>
                    <form action="{{ route('admin.subscriptions.extend', $payment) }}" method="POST">
                        @csrf
                        <div class="flex gap-2">
                            <input type="number" name="days" value="30" min="1" max="365"
                                   class="flex-1 px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                            <span class="flex items-center text-sm text-gray-500">jours</span>
                        </div>
                        <button type="submit" class="w-full mt-3 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Prolonger
                        </button>
                    </form>
                </div>
            @endif

            {{-- Actions --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                <div class="space-y-2">
                    @if($payment->status === 'completed' && $payment->is_active)
                        <form action="{{ route('admin.subscriptions.cancel', $payment) }}" method="POST" onsubmit="return confirm('Annuler cet abonnement et rembourser ?')">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Annuler et rembourser
                            </button>
                        </form>
                    @endif
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
                            <div class="text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @if($payment->expires_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full {{ $payment->is_active ? 'bg-emerald-500' : 'bg-gray-400' }} mt-1.5"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Expiration</div>
                                <div class="text-gray-500">{{ $payment->expires_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Dernière MAJ</div>
                            <div class="text-gray-500">{{ $payment->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
