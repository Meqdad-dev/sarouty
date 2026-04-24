@extends('layouts.admin')

@section('title', 'Dashboard Admin – Sarouty')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Vue globale de la plateforme et activité récente')



@section('content')
    {{-- Main Statistics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        {{-- Total Users --}}
        <div class="panel rounded-2xl p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-indigo-500/20 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        @if($stats['users_this_week'] > 0)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 15.586 6H12z" clip-rule="evenodd"/></svg>
                                +{{ $stats['users_this_week'] }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['users_total']) }}</div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Total Utilisateurs</div>
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span class="text-xs text-gray-500">{{ $stats['users_agents'] }} agents</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs text-gray-500">{{ $stats['users_today'] }} aujourd'hui</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Listings --}}
        <div class="panel rounded-2xl p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-amber-500/20 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-xl shadow-amber-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    @if($stats['listings_today'] > 0)
                        <span class="text-xs font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                            +{{ $stats['listings_today'] }} aujourd'hui
                        </span>
                    @endif
                </div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['listings_total']) }}</div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Total Annonces</div>
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs text-gray-500">{{ $stats['listings_active'] }} actives</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-xs text-gray-500">{{ $stats['listings_pending'] }} en attente</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Validation --}}
        <div class="panel rounded-2xl p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300 @if($stats['listings_pending'] > 0) ring-2 ring-amber-400 ring-offset-2 @endif">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-orange-500/20 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-xl shadow-orange-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    @if($stats['listings_pending'] > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-orange-600 bg-orange-100 px-2.5 py-1 rounded-full animate-pulse">
                            Action requise
                        </span>
                    @endif
                </div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['listings_pending']) }}</div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">En attente de validation</div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-gold hover:text-gold-dark transition flex items-center gap-1">
                        Voir les annonces en attente
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Revenue --}}
        <div class="panel rounded-2xl p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-xl shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">MAD</span>
                </div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['revenue_month'], 0, ',', ' ') }}</div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Revenus ce mois</div>
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-xs text-gray-500">Total: {{ number_format($stats['revenue_total'], 0, ',', ' ') }} MAD</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="panel rounded-xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['messages_total']) }}</div>
                <div class="text-xs text-gray-500">Messages</div>
            </div>
        </div>

        <div class="panel rounded-xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['reports_pending']) }}</div>
                <div class="text-xs text-gray-500">Signalements</div>
            </div>
            @if($stats['reports_pending'] > 0)
                <a href="{{ route('admin.reports.index') }}" class="ml-auto text-xs text-gold hover:underline">Voir</a>
            @endif
        </div>

        <div class="panel rounded-xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['users_agents']) }}</div>
                <div class="text-xs text-gray-500">Agents</div>
            </div>
        </div>

        <div class="panel rounded-xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['payments_completed']) }}</div>
                <div class="text-xs text-gray-500">Paiements validés</div>
                <div class="text-[11px] text-gray-400">{{ number_format($stats['payments_pending']) }} en attente · {{ number_format($stats['payments_failed']) }} échoués</div>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="ml-auto text-xs text-gold hover:underline">Voir</a>
        </div>

        <div class="panel rounded-xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-gold/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['listings_active']) }}</div>
                <div class="text-xs text-gray-500">Annonces actives</div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="panel rounded-2xl p-6 xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Croissance utilisateurs</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Évolution sur les 30 derniers jours</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1.5 text-xs font-medium text-gray-500">
                        <span class="w-3 h-0.5 rounded bg-gold"></span>
                        Utilisateurs
                    </span>
                </div>
            </div>
            <div class="h-64 sm:h-72">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="panel rounded-2xl p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Types d'annonces</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Répartition par transaction</p>
                </div>
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Pending Listings --}}
        <div class="panel rounded-2xl p-6 xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Annonces en attente</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">À valider par l'équipe</p>
                    </div>
                </div>
                <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}" class="text-sm font-medium text-gold hover:text-gold-dark transition flex items-center gap-1">
                    Tout voir
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @if($pendingListings->isEmpty())
                <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border-2 border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune annonce en attente</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendingListings as $listing)
                        <div class="admin-dashboard-list-item p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                            <div class="admin-dashboard-list-media rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-700">
                                @if($listing->thumbnail_url)
                                    <img src="{{ $listing->thumbnail_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="admin-dashboard-list-content">
                                <div class="font-medium text-gray-900 dark:text-white truncate">{{ $listing->title }}</div>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                    <span>{{ $listing->city }}</span>
                                    <span>•</span>
                                    <span>{{ $listing->user->name ?? 'Utilisateur' }}</span>
                                    <span>•</span>
                                    <span>{{ $listing->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="admin-dashboard-list-actions transition-opacity">
                                <a href="{{ route('admin.listings.show', $listing) }}" class="nav-ajax admin-action-btn admin-action-btn--gold bg-white dark:bg-gray-700" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form action="{{ route('admin.listings.approve', $listing) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="admin-action-btn admin-action-btn--success bg-white dark:bg-gray-700" title="Approuver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            </div>
                            <div class="admin-dashboard-list-badges">
                                @if($listing->is_sponsored)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                        ⭐ Sponsorisée
                                        @if(($listing->sponsorship?->amount ?? 0) == 0)
                                            <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Abonnement</span>
                                        @endif
                                    </span>
                                @endif
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">En attente</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-6">
            {{-- Latest Payments --}}
            <div class="panel rounded-2xl p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Paiements</h2>
                    </div>
                    <a href="{{ route('admin.payments.index') }}" class="text-xs font-medium text-gold hover:text-gold-dark transition">Voir tout</a>
                </div>
                @if($latestPayments->isEmpty())
                    <div class="text-center py-6 text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        <p class="text-xs">Aucun paiement</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($latestPayments as $payment)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                <img src="{{ $payment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($payment->user->name ?? 'U') }}" alt="" class="w-9 h-9 rounded-full object-cover">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $payment->user->name ?? 'Utilisateur' }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($payment->plan) }} · {{ $payment->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="text-sm font-semibold text-emerald-600">{{ number_format($payment->amount, 0, ',', ' ') }} MAD</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent Reports --}}
            <div class="panel rounded-2xl p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Signalements</h2>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-medium text-gold hover:text-gold-dark transition">Voir tout</a>
                </div>
                @if($recentReports->isEmpty())
                    <div class="text-center py-6 text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs">Aucun signalement</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentReports as $report)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                <div class="w-9 h-9 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $report->listing->title ?? 'Annonce supprimée' }}</div>
                                    <div class="text-xs text-gray-500">{{ $report->reason_label ?? $report->reason }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="panel rounded-2xl p-6 mt-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Section sponsorisée</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Annonces sponsorisées visibles dans l'admin</p>
                </div>
            </div>
            <a href="{{ route('admin.sponsorships.index') }}" class="nav-ajax text-sm font-medium text-gold hover:text-gold-dark transition">Voir tout</a>
        </div>

        @if($sponsoredListings->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                Aucune annonce sponsorisée pour le moment.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($sponsoredListings as $listing)
                    <div class="rounded-2xl border border-amber-200/70 bg-amber-50/50 dark:border-amber-900/40 dark:bg-amber-950/10 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">⭐ Sponsorisée</span>
                                    @if(($listing->sponsorship?->amount ?? 0) == 0)
                                        <span class="inline-flex items-center rounded-full bg-white/80 px-2 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-200">Abonnement</span>
                                    @endif
                                </div>
                                <h3 class="mt-3 font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $listing->title }}</h3>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $listing->location_label ?: $listing->city }}</p>
                            </div>
                            <a href="{{ route('admin.listings.show', $listing) }}" class="nav-ajax admin-action-btn admin-action-btn--gold bg-white dark:bg-gray-800" title="Voir">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $listing->user->name ?? 'Utilisateur' }}</span>
                            <span>{{ $listing->sponsored_until?->format('d/m/Y') ?? 'En attente' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Users Table --}}
    <div class="panel rounded-2xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Derniers utilisateurs</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Inscriptions récentes</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gold hover:text-gold-dark transition flex items-center gap-1">
                Tout voir
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        @if($latestUsers->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">Aucun utilisateur récent</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 pb-3 pr-4">Utilisateur</th>
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 pb-3 pr-4">Email</th>
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 pb-3 pr-4">Rôle</th>
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 pb-3 pr-4">Date</th>
                            <th class="text-right text-xs font-semibold text-gray-500 dark:text-gray-400 pb-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestUsers as $user)
                            <tr class="border-b border-gray-50 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 pr-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="" class="w-9 h-9 rounded-full object-cover">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 pr-4 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="py-4 pr-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{
                                        match($user->role) {
                                            'admin' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                            'agent' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                        }
                                    }}">{{ ucfirst($user->role ?? 'particulier') }}</span>
                                </td>
                                <td class="py-4 pr-4 text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="py-4 text-right">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-gold hover:text-gold-dark text-sm font-medium">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const userGrowthData = @json($userGrowth);
        const listingTypeData = @json($listingsByType);

        // Generate last 30 days
        const days = [];
        for (let i = 29; i >= 0; i--) {
            const d = new Date();
            d.setDate(d.getDate() - i);
            days.push(d.toISOString().split('T')[0]);
        }

        const userCounts = days.map(d => userGrowthData[d]?.count ?? 0);

        // User Growth Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const gradient = activityCtx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(200, 150, 62, 0.15)');
        gradient.addColorStop(1, 'rgba(200, 150, 62, 0)');

        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: days.map(d => d.split('-').reverse().slice(0, 2).join('/')),
                datasets: [{
                    label: 'Utilisateurs',
                    data: userCounts,
                    borderColor: '#C8963E',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#C8963E',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false },
                        border: { display: false },
                        ticks: { 
                            color: '#9ca3af',
                            font: { size: 11 },
                            maxRotation: 0,
                            maxTicksLimit: 7,
                        } 
                    },
                    y: { 
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        border: { display: false },
                        ticks: { 
                            color: '#9ca3af',
                            font: { size: 11 },
                            precision: 0,
                        } 
                    },
                }
            }
        });

        // Type Distribution Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: listingTypeData.map(d => ({ 
                    vente: 'Vente', 
                    location: 'Location', 
                    neuf: 'Neuf', 
                    vacances: 'Vacances' 
                }[d.transaction_type] || d.transaction_type)),
                datasets: [{
                    data: listingTypeData.map(d => d.count),
                    backgroundColor: [
                        '#C8963E',
                        '#3B82F6', 
                        '#10B981', 
                        '#8B5CF6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { 
                            color: '#6b7280',
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12 }
                        } 
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 8,
                    }
                }
            }
        });
    </script>
@endsection
