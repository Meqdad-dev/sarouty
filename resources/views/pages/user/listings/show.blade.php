@extends('layouts.user')

@section('title', $listing->title . ' – Mes annonces')
@section('page_title', Str::limit($listing->title, 60))
@section('page_subtitle', 'Référence : DM-' . str_pad($listing->id, 6, '0', STR_PAD_LEFT))

@section('top_actions')
    <div class="flex gap-3">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
            ← Retour
        </a>
        <a href="{{ route('user.listings.edit', $listing) }}"
           class="flex items-center gap-2 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 text-sm font-medium px-4 py-2 rounded-xl transition-opacity hover:opacity-90 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </a>
        @if($listing->status === 'active')
            <a href="{{ route('listings.show', $listing) }}" target="_blank"
               class="flex items-center gap-2 bg-gold hover:bg-gold-dark text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Voir en ligne
            </a>
        @endif
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
    {{-- Détail annonce --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Statut --}}
        @if($listing->status === 'pending')
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 rounded-2xl px-5 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-yellow-800 dark:text-yellow-400">En attente de validation</p>
                    <p class="text-sm text-yellow-600 dark:text-yellow-500 mt-0.5">Notre équipe examinera votre annonce sous 24h ouvrées.</p>
                </div>
            </div>
        @elseif($listing->status === 'rejected')
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-2xl px-5 py-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="font-semibold text-red-700 dark:text-red-400">Annonce refusée</p>
                </div>
                @if($listing->rejection_reason)
                    <p class="text-sm text-red-600 dark:text-red-300 bg-red-100 dark:bg-red-900/40 rounded-xl px-4 py-3 mb-3">
                        <strong class="font-semibold">Motif :</strong> {{ $listing->rejection_reason }}
                    </p>
                @endif
                <a href="{{ route('user.listings.edit', $listing) }}"
                   class="inline-flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Modifier et resoumettre →
                </a>
            </div>
        @elseif($listing->status === 'active')
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 rounded-2xl px-5 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-green-700 dark:text-green-400">Annonce active et visible</p>
                    <p class="text-sm text-green-600 dark:text-green-500 mt-0.5">{{ number_format($listing->views) }} vue(s) · {{ $listing->favorites->count() }} favori(s)</p>
                </div>
            </div>
        @endif

        {{-- Infos principales --}}
        <div class="panel rounded-[24px] p-6 lg:p-8">
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-gold/10 text-gold text-xs font-semibold px-3 py-1.5 rounded-full">{{ $listing->transaction_label }}</span>
                <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs px-3 py-1.5 rounded-full font-medium">{{ $listing->property_label }}</span>
            </div>

            <p class="font-display text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $listing->formatted_price }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $listing->city }}{{ $listing->zone ? ' – ' . $listing->zone : '' }}
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center mb-8">
                @if($listing->surface)
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium lowercase">Surface</span>
                        </div>
                        <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->surface }} m²</div>
                    </div>
                @endif
                @if($listing->rooms)
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11V9a4 4 0 014-4h10a4 4 0 014 4v2m-18 8v-8h18v8M3 15h18"/></svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium lowercase">Chambres</span>
                        </div>
                        <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->rooms }}</div>
                    </div>
                @endif
                @if($listing->bathrooms)
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v2H2V6c0-1.1.9-2 2-2zM2 8h20v9c0 2.2-1.8 4-4 4H6c-2.2 0-4-1.8-4-4V8zM8 4V2m8 2V2M12 4V2"/></svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium lowercase">SDB</span>
                        </div>
                        <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->bathrooms }}</div>
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                <h3 class="font-display font-semibold text-gray-900 dark:text-white text-lg mb-3">Description</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{!! nl2br(e($listing->description)) !!}</p>
            </div>
            
            @if(count(array_filter([
                $listing->furnished, $listing->parking, $listing->elevator, 
                $listing->pool, $listing->garden, $listing->terrace, $listing->security
            ])) > 0)
            <div class="border-t border-gray-100 dark:border-gray-800 pt-6 mt-6">
                <h3 class="font-display font-semibold text-gray-900 dark:text-white text-lg mb-4">Équipements</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-y-3 gap-x-4">
                    @if($listing->furnished)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 11V9a2 2 0 012-2h12a2 2 0 012 2v2m-14 5h14m2-5v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="2"/></svg> Meublé</div>@endif
                    @if($listing->parking)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 18a2 2 0 104 0 2 2 0 00-4 0zM15 18a2 2 0 104 0 2 2 0 00-4 0zM5 18H4v-4l1.5-4.5h13L20 14v4h-1M5 14h14M8 10V6a2 2 0 012-2h4a2 2 0 012 2v4" stroke-width="2"/></svg> Parking</div>@endif
                    @if($listing->elevator)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 9l5-5 5 5M7 15l5 5 5-5" stroke-width="2"/></svg> Ascenseur</div>@endif
                    @if($listing->pool)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 12c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2M21 16c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2" stroke-width="2"/></svg> Piscine</div>@endif
                    @if($listing->garden)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 21a9 9 0 009-9 9 9 0 00-9-9 9 9 0 00-9 9 9 9 0 009 9zM12 3v18" stroke-width="2"/></svg> Jardin</div>@endif
                    @if($listing->terrace)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707" stroke-width="2"/></svg> Terrasse</div>@endif
                    @if($listing->security)<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-5 h-5 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622" stroke-width="2"/></svg> Sécurité</div>@endif
                </div>
            </div>
            @endif
        </div>

        {{-- Messages reçus --}}
        @if($listing->messages->isNotEmpty())
            <div class="panel rounded-[24px] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center justify-between">
                    <h3 class="font-display font-semibold text-gray-900 dark:text-white">Derniers messages</h3>
                    <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-bold px-2.5 py-1 rounded-full">{{ $listing->messages->count() }}</span>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($listing->messages->take(5) as $msg)
                        <div class="px-6 py-5 {{ !$msg->is_read ? 'bg-gold/5 dark:bg-gold/10' : '' }} transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $msg->sender_name }}</p>
                                        <p class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $msg->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400 mb-3 font-medium">
                                        <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> {{ $msg->sender_email }}</span>
                                        @if($msg->sender_phone) <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $msg->sender_phone }}</span> @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/50 rounded-xl px-4 py-3 border border-gray-100 dark:border-gray-800">{{ $msg->message }}</p>
                                </div>
                                @if(!$msg->is_read)
                                    <span class="w-2.5 h-2.5 bg-gold rounded-full flex-shrink-0 shadow-sm shadow-gold/40 mt-1"></span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($listing->messages->count() > 5)
                    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 text-center">
                        <a href="{{ route('user.messages.index') }}" class="text-sm text-gold font-medium hover:text-gold-dark transition-colors">Voir tous les messages →</a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Sidebar actions --}}
    <div class="space-y-6">
        {{-- Photos Grid Mini --}}
        @if($listing->images->isNotEmpty())
            <div class="panel rounded-[24px] overflow-hidden p-3">
                <div class="grid grid-cols-2 gap-2 h-40">
                    @foreach($listing->images->take(4) as $idx => $img)
                        @if($idx === 3 && $listing->images->count() > 4)
                            <div class="relative rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                                <img src="{{ $img->url }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">+{{ $listing->images->count() - 3 }}</span>
                                </div>
                            </div>
                        @else
                            <div class="{{ $idx === 0 && $listing->images->count() === 3 ? 'col-span-2' : '' }} {{ $idx === 0 && $listing->images->count() <= 2 ? 'col-span-2 h-full' : '' }} rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800">
                                <img src="{{ $img->url }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sponsorship Status --}}
        @if($listing->isCurrentlySponsored())
            <div class="panel rounded-[24px] p-5 border-gold/30 bg-gold/5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gold text-sm">⭐ Annonce sponsorisee</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Expire le {{ $listing->sponsored_until->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Jours restants</span>
                    <span class="font-bold text-gold">{{ $listing->sponsored_remaining_days }} jour(s)</span>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="panel rounded-[24px] p-6 space-y-3">
            <h3 class="font-display font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
            <a href="{{ route('user.listings.edit', $listing) }}"
               class="w-full flex items-center justify-center gap-2 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:opacity-90 font-semibold py-3 rounded-xl text-sm transition-opacity shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier l'annonce
            </a>
            @if($listing->status === 'active')
                <a href="{{ route('listings.show', $listing) }}" target="_blank"
                   class="w-full flex items-center justify-center gap-2 bg-gold hover:bg-gold-dark text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-sm shadow-gold/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Voir en ligne
                </a>
                @if(!$listing->isCurrentlySponsored())
                    <a href="{{ route('user.listings.sponsor', $listing) }}"
                       class="w-full flex items-center justify-center gap-2 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 text-amber-700 dark:text-amber-400 font-semibold py-3 rounded-xl text-sm transition-colors border border-amber-200 dark:border-amber-800/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Sponsoriser cette annonce
                    </a>
                @endif
            @endif
            <form action="{{ route('user.listings.destroy', $listing) }}" method="POST"
                  onsubmit="return confirm('Confirmez-vous la suppression définitive de cette annonce ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 mt-4 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 font-semibold py-3 rounded-xl text-sm transition-colors border border-red-100 dark:border-red-800/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </form>
        </div>

        {{-- Stats --}}
        <div class="panel rounded-[24px] p-6">
            <h3 class="font-display font-semibold text-gray-900 dark:text-white mb-4 text-sm uppercase tracking-wider text-center">Performances</h3>
            <div class="space-y-4">
                @foreach([
                    ['label' => 'Vues totales',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>', 'value' => number_format($listing->views)],
                    ['label' => 'Favoris',         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>', 'value' => $listing->favorites->count()],
                    ['label' => 'Messages',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>', 'value' => $listing->messages->count()],
                ] as $stat)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 text-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $stat['icon'] !!}
                            </svg>
                            <span>{{ $stat['label'] }}</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</span>
                    </div>
                @endforeach
                
                <hr class="border-gray-100 dark:border-gray-800 my-4">
                
                @foreach([
                    ['label' => 'Publiée',  'value' => $listing->created_at->format('d/m/Y')],
                    ['label' => 'Modifiée', 'value' => $listing->updated_at->diffForHumans()],
                ] as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</span>
                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $stat['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
