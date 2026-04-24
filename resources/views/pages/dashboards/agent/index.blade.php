@extends('layouts.user')

@section('title', 'Tableau de bord – Mon Espace')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Gérez vos annonces et visualisez vos statistiques')

@section('top_actions')
    <a href="{{ route('user.favorites') }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        Favoris
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Annonces actives', 'value' => $stats['listings_active'], 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'bg' => 'from-emerald-500 to-emerald-600'],
            ['label' => 'En attente',       'value' => $stats['listings_pending'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'from-amber-500 to-amber-600'],
            ['label' => 'Vues totales',     'value' => number_format($stats['total_views']), 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'bg' => 'from-blue-500 to-blue-600'],
            ['label' => 'Nouveaux Messages','value' => $stats['messages_unread'], 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'bg' => 'from-violet-500 to-violet-600'],
        ] as $stat)
            <div class="panel rounded-2xl p-5 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $stat['bg'] }} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    @if($stat['label'] === 'Nouveaux Messages' && $stats['messages_unread'] > 0)
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs font-bold">{{ $stats['messages_unread'] }}</span>
                    @endif
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $stat['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>


    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- My Listings or Favorites --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="panel rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h2 class="font-semibold text-gray-900 dark:text-white text-lg">Mes annonces</h2>
                    </div>
                </div>

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-white/70 dark:bg-gray-900/10">
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('user.listings.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold px-4 py-2 text-sm font-semibold text-white transition hover:bg-gold-dark">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Publier une annonce
                        </a>
                        @if($canCreateSponsoredListing)
                            <a href="{{ route('user.listings.create', ['sponsored' => 1]) }}" class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:border-amber-400 hover:bg-amber-100">
                                <span>Sponsored</span>
                                <span class="inline-flex items-center rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">{{ auth()->user()->plan_label }}</span>
                            </a>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $sponsoredActiveCount }} annonce(s) sponsorisée(s) active(s)</span>
                        @endif
                    </div>
                </div>

                @if($listings->isEmpty())
                    <div class="text-center py-16">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1 text-lg">Aucune annonce publiée</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Commencez par créer votre première annonce immobilière.</p>
                        <a href="{{ route('user.listings.create') }}"
                           class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Publier maintenant
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($listings as $listing)
                            <div class="dashboard-list-row px-4 sm:px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                {{-- Image --}}
                                <div class="dashboard-list-media rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 shadow-sm">
                                    @if($listing->thumbnail_url)
                                        <img src="{{ $listing->thumbnail_url }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="dashboard-list-body">
                                    <h3 class="font-medium text-gold text-sm truncate group-hover:text-gold-dark transition-colors">@if($listing->isCurrentlySponsored())⭐ @endif{{ $listing->title }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $listing->city }}
                                        </span>
                                        <span class="text-xs text-gray-300 dark:text-gray-600">•</span>
                                        <span class="text-xs font-semibold text-gold">{{ $listing->formatted_price }}</span>
                                        <span class="text-xs text-gray-300 dark:text-gray-600">•</span>
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            {{ $listing->views }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Statut --}}
                                <div class="dashboard-list-status">
                                    @if($listing->isCurrentlySponsored())
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Sponsorisee
                                        </span>
                                    @endif
                                    @php
                                        $badge = match($listing->status) {
                                            'active'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'pending'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            'sold'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            default    => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                        {{ $listing->status_label }}
                                    </span>
                                </div>

                                {{-- Actions --}}
                                <div class="dashboard-list-actions lg:opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('user.listings.show', $listing) }}"
                                       class="action-icon-btn action-icon-btn--neutral" title="Voir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('user.listings.edit', $listing) }}"
                                       class="action-icon-btn action-icon-btn--gold" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($canCreateSponsoredListing && !$listing->isCurrentlySponsored())
                                        <a href="{{ route('user.listings.edit', ['listing' => $listing, 'sponsored' => 1]) }}"
                                           class="action-icon-btn action-icon-btn--amber" title="Sponsored">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <form action="{{ route('user.listings.destroy', $listing) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cette annonce ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Supprimer"
                                                class="action-icon-btn action-icon-btn--danger">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($listings->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                            {{ $listings->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Right Sidebar Content --}}
        <div>
            {{-- Quota Section --}}
            @php
                $quota   = auth()->user()->plan_quota;
                $usedPct = $quota > 0 ? min(100, ($listingsUsed / $quota) * 100) : 100;
            @endphp
            <div class="panel rounded-2xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-gold/10 to-transparent rounded-full -translate-y-1/3 translate-x-1/3"></div>
                
                <div class="flex items-center gap-4 mb-5 relative">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center shadow-lg shadow-gold/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Quota mensuel</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">Plan {{ auth()->user()->plan ?? 'Gratuit' }}</p>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $listingsUsed }}</span>
                        <span class="text-sm font-medium text-gray-500">sur {{ $quota }} annonces</span>
                    </div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 relative"
                             style="width:{{ $usedPct }}%; background:{{ $usedPct >= 90 ? 'linear-gradient(to right, #EF4444, #DC2626)' : ($usedPct >= 70 ? 'linear-gradient(to right, #F59E0B, #D97706)' : 'linear-gradient(to right, #C8963E, #9B6E22)') }}">
                            @if($usedPct > 0)
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($usedPct >= 80)
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('tarifs') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border-2 border-gold text-gold hover:bg-gold hover:text-white transition-colors">
                            Augmenter mon quota
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Quick action card --}}
            <a href="{{ route('user.profile') }}" class="mt-6 panel-soft rounded-2xl p-6 flex flex-col items-center justify-center text-center group hover:border-gold/30 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 mb-3 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-medium text-gray-900 dark:text-white text-base">Completer votre profil</h3>
                <p class="text-xs text-gray-500 mt-1">Un profil complet inspire plus de confiance.</p>
            </a>

            {{-- Sponsored ads summary --}}
            @php
                $sponsoredListings = auth()->user()->listings()
                    ->where('is_sponsored', true)
                    ->where('sponsored_until', '>', now())
                    ->get();
            @endphp
            @if($sponsoredListings->isNotEmpty())
                <div class="mt-6 panel rounded-2xl p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Annonces sponsorisees</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sponsoredListings->count() }} annonce(s) active(s)</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($sponsoredListings->take(3) as $sp)
                            <div class="flex items-center justify-between text-sm">
                                <a href="{{ route('user.listings.show', $sp) }}" class="text-gold hover:text-gold-dark truncate flex-1 mr-3">{{ Str::limit($sp->title, 30) }}</a>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $sp->sponsored_remaining_days }}j restant(s)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
