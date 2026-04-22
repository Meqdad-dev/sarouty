@extends('layouts.admin')

@section('title', 'Paiement #' . $payment->id . ' – Sarouty')
@section('page_title', 'Détails du paiement')
@section('page_subtitle', $payment->plan_label . ' - ' . ($payment->user->name ?? 'Utilisateur'))

@section('top_actions')
    @if($payment->status === 'completed')
        <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" onsubmit="return confirm('Rembourser ce paiement ?')" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 text-red-600 px-4 py-2.5 text-sm font-semibold hover:bg-red-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Rembourser
            </button>
        </form>
    @endif
@endsection

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="xl:col-span-2 space-y-6">
            {{-- Payment Status Card --}}
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
                </div>

                {{-- Amount --}}
                <div class="text-center p-6 rounded-xl bg-gradient-to-br from-gold/10 to-amber-50 dark:from-gold/20 dark:to-amber-900/10 border border-gold/20 mb-6">
                    <div class="text-5xl font-bold text-gold">{{ $payment->formatted_amount }}</div>
                    <div class="text-sm text-gray-500 mt-1">Montant du paiement</div>
                </div>

                {{-- Details Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $payment->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">Date</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $payment->created_at->format('H:i') }}</div>
                        <div class="text-xs text-gray-500">Heure</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">#{{ $payment->id }}</div>
                        <div class="text-xs text-gray-500">ID Paiement</div>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $payment->expires_at ? $payment->expires_at->format('d/m/Y') : '-' }}</div>
                        <div class="text-xs text-gray-500">Expiration</div>
                    </div>
                </div>
            </div>

            {{-- Transaction Details --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Détails de la transaction
                </h3>

                <div class="space-y-4">
                    @if($payment->stripe_session_id)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Stripe Session ID</label>
                            <code class="block w-full text-xs bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-xl overflow-x-auto">{{ $payment->stripe_session_id }}</code>
                        </div>
                    @endif

                    @if($payment->stripe_payment_intent)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Stripe Payment Intent</label>
                            <code class="block w-full text-xs bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-xl overflow-x-auto">{{ $payment->stripe_payment_intent }}</code>
                        </div>
                    @endif

                    @if(!$payment->stripe_session_id && !$payment->stripe_payment_intent)
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <p>Ce paiement a été créé manuellement par un administrateur</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User's Recent Listings --}}
            @if($payment->user && $payment->user->listings->count() > 0)
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Annonces récentes de l'utilisateur</h3>
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client</h3>
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
                            <span class="text-gray-500">Statut</span>
                            <span class="font-medium {{ $payment->user->is_active ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $payment->user->status_label }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Inscrit le</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $payment->user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $payment->user) }}" class="block w-full text-center py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium hover:border-gold/40 hover:text-gold transition">
                        Voir le profil
                    </a>
                </div>
            @endif

            {{-- Timeline --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Historique</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-gold mt-1.5"></div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Création</div>
                            <div class="text-gray-500">{{ $payment->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                    </div>
                    @if($payment->status === 'completed' && $payment->updated_at->ne($payment->created_at))
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Confirmé</div>
                                <div class="text-gray-500">{{ $payment->updated_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    @if($payment->status === 'refunded')
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-purple-500 mt-1.5"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Remboursé</div>
                                <div class="text-gray-500">{{ $payment->updated_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    @if($payment->expires_at && $payment->status === 'completed')
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Expiration</div>
                                <div class="text-gray-500">{{ $payment->expires_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User's Payment History --}}
            @if($userPayments->count() > 0)
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Autres paiements</h3>
                    <div class="space-y-2">
                        @foreach($userPayments as $p)
                            <a href="{{ route('admin.payments.show', $p) }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $p->plan_label }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->created_at->format('d/m/Y') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $p->formatted_amount }}</div>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs
                                        @if($p->status === 'completed') bg-emerald-100 text-emerald-700
                                        @elseif($p->status === 'pending') bg-amber-100 text-amber-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $p->status_label }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.payments.index') }}" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium hover:border-gold/40 hover:text-gold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
