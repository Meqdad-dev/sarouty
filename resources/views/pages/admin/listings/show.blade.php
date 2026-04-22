@extends('layouts.admin')

@section('title', 'Annonce #' . $listing->id . ' – Administration')
@section('page_title', 'Détails de l\'annonce')
@section('page_subtitle', $listing->title)

@section('top_actions')
    <a href="{{ route('admin.listings.edit', $listing) }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
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
            {{-- Images --}}
            @if($listing->images->isNotEmpty())
                <div class="panel rounded-2xl overflow-hidden">
                    <div class="grid grid-cols-3 gap-1 h-72">
                        @foreach($listing->images->take(3) as $index => $image)
                            <div class="{{ $index === 0 ? 'col-span-2 row-span-1' : '' }} overflow-hidden">
                                <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            </div>
                        @endforeach
                    </div>
                    @if($listing->images->count() > 3)
                        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-800/50 text-xs text-gray-500 text-right">
                            + {{ $listing->images->count() - 3 }} autres photo(s)
                        </div>
                    @endif
                </div>
            @endif

            {{-- Info --}}
            <div class="panel rounded-2xl p-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="bg-gold/10 text-gold text-xs font-semibold px-3 py-1.5 rounded-full">{{ $listing->transaction_label }}</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium px-3 py-1.5 rounded-full">{{ $listing->property_label }}</span>
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                        @if($listing->status === 'active') bg-emerald-100 text-emerald-700
                        @elseif($listing->status === 'pending') bg-amber-100 text-amber-700
                        @elseif($listing->status === 'rejected') bg-red-100 text-red-700
                        @elseif($listing->status === 'sold') bg-blue-100 text-blue-700
                        @elseif($listing->status === 'rented') bg-purple-100 text-purple-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $listing->status_label }}
                    </span>
                    @if($listing->featured)
                        <span class="bg-gold/20 text-gold text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Vedette
                        </span>
                    @endif
                    @if($listing->is_sponsored)
                        <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1.5 dark:bg-amber-900/30 dark:text-amber-300">
                            <span>⭐ Sponsorisée</span>
                            @if(($listing->sponsorship?->amount ?? 0) == 0)
                                <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Abonnement</span>
                            @endif
                        </span>
                    @endif
                </div>

                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $listing->title }}</h2>
                <p class="text-2xl font-bold text-gold mb-4">{{ $listing->formatted_price }}</p>
                <p class="text-sm text-gray-500 mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $listing->location_label ?: $listing->city }}
                </p>

                {{-- Features Grid --}}
                <div class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-6 text-center">
                    @if($listing->surface)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <svg class="w-5 h-5 mx-auto text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->surface }} m²</div>
                        </div>
                    @endif
                    @if($listing->rooms)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <svg class="w-5 h-5 mx-auto text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11V9a4 4 0 014-4h10a4 4 0 014 4v2m-18 8v-8h18v8M3 15h18"/></svg>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->rooms }}</div>
                        </div>
                    @endif
                    @if($listing->bathrooms)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <svg class="w-5 h-5 mx-auto text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16c1.1 0 2 .9 2 2v2H2V6c0-1.1.9-2 2-2zM2 8h20v9c0 2.2-1.8 4-4 4H6c-2.2 0-4-1.8-4-4V8zM8 4V2m8 2V2M12 4V2"/></svg>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->bathrooms }}</div>
                        </div>
                    @endif
                    @if($listing->floor !== null)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <svg class="w-5 h-5 mx-auto text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $listing->floor }}</div>
                        </div>
                    @endif
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <svg class="w-5 h-5 mx-auto text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <div class="font-bold text-gray-900 dark:text-white text-lg">{{ number_format($listing->views) }}</div>
                    </div>
                </div>

                {{-- Amenities --}}
                @if($listing->furnished || $listing->parking || $listing->elevator || $listing->pool || $listing->garden || $listing->terrace || $listing->security)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Équipements</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($listing->furnished)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 11V9a2 2 0 012-2h12a2 2 0 012 2v2m-14 5h14m2-5v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="2"/></svg>
                                    Meublé
                                </span>
                            @endif
                            @if($listing->parking)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 18a2 2 0 104 0 2 2 0 00-4 0zM15 18a2 2 0 104 0 2 2 0 00-4 0zM5 18H4v-4l1.5-4.5h13L20 14v4h-1M5 14h14M8 10V6a2 2 0 012-2h4a2 2 0 012 2v4" stroke-width="2"/></svg>
                                    Parking
                                </span>
                            @endif
                            @if($listing->elevator)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 9l5-5 5 5M7 15l5 5 5-5" stroke-width="2"/></svg>
                                    Ascenseur
                                </span>
                            @endif
                            @if($listing->pool)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 12c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2M21 16c-2.4 0-2.4 2-4.8 2s-4.8-2-7.2-2-4.8 2-7.2 2" stroke-width="2"/></svg>
                                    Piscine
                                </span>
                            @endif
                            @if($listing->garden)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 21a9 9 0 009-9 9 9 0 00-9-9 9 9 0 00-9 9 9 9 0 009 9zM12 3v18" stroke-width="2"/></svg>
                                    Jardin
                                </span>
                            @endif
                            @if($listing->terrace)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707" stroke-width="2"/></svg>
                                    Terrasse
                                </span>
                            @endif
                            @if($listing->security)
                                <span class="px-3 py-1.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622" stroke-width="2"/></svg>
                                    Sécurité
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Description</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{!! nl2br(e($listing->description)) !!}</p>
                </div>
            </div>

            {{-- Reports --}}
            @if($listing->reports->isNotEmpty())
                <div class="panel rounded-2xl p-6 border-red-200 dark:border-red-900/50">
                    <h3 class="font-semibold text-red-700 dark:text-red-400 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Signalements ({{ $listing->reports->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($listing->reports as $report)
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-sm">
                                <div class="flex justify-between mb-2">
                                    <span class="font-semibold text-red-700 dark:text-red-400">{{ $report->reason_label }}</span>
                                    <span class="text-red-400 text-xs">{{ $report->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($report->description)
                                    <p class="text-gray-600 dark:text-gray-400">{{ $report->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- AI Moderation --}}
            @isset($aiModeration)
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        Analyse IA
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-center mb-4">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3">
                            <div class="text-2xl font-bold {{ $aiModeration->risk_score > 50 ? 'text-red-500' : 'text-emerald-500' }}">{{ $aiModeration->risk_score }}%</div>
                            <div class="text-xs text-gray-500">Score de Risque</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3">
                            <div class="text-2xl font-bold {{ $aiModeration->approved ? 'text-emerald-500' : 'text-red-500' }}">{{ $aiModeration->approved ? 'Oui' : 'Non' }}</div>
                            <div class="text-xs text-gray-500">Approuvé par l'IA</div>
                        </div>
                    </div>
                    @if($aiModeration->reason)
                        <div class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <span class="font-medium">Remarque:</span> {{ $aiModeration->reason }}
                        </div>
                    @endif
                    @if(!empty($aiModeration->flags))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach((is_string($aiModeration->flags) ? json_decode($aiModeration->flags, true) : $aiModeration->flags) as $flag)
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded">Alerte: {{ $flag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endisset
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Owner --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Propriétaire</h3>
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $listing->user->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $listing->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $listing->user->role_label }}</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 space-y-2">
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $listing->user->email }}
                    </p>
                    @if($listing->user->phone)
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $listing->user->phone }}
                        </p>
                    @endif
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Membre depuis {{ $listing->user->created_at->format('M Y') }}
                    </p>
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        {{ $listing->user->listings()->count() }} annonce(s)
                    </p>
                </div>
                <a href="{{ route('admin.users.show', $listing->user) }}"
                   class="mt-4 block text-center text-sm text-gold hover:underline">
                    Voir le profil →
                </a>
            </div>

            {{-- Actions --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>

                <div class="space-y-3">
                    {{-- Approve --}}
                    @if($listing->status !== 'active')
                        <form action="{{ route('admin.listings.approve', $listing) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Valider l'annonce
                            </button>
                        </form>
                    @endif

                    {{-- Feature --}}
                    <form action="{{ route('admin.listings.feature', $listing) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 {{ $listing->featured ? 'bg-gold/20 text-gold' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gold/10 hover:text-gold' }} text-sm font-semibold py-3 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="{{ $listing->featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            {{ $listing->featured ? 'Retirer des vedettes' : 'Mettre en avant' }}
                        </button>
                    </form>

                    {{-- Reject --}}
                    @if($listing->status !== 'rejected')
                        <div x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-center gap-2 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 text-sm font-semibold py-3 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Refuser l'annonce
                            </button>
                            <div x-show="open" x-transition class="mt-3">
                                <form action="{{ route('admin.listings.reject', $listing) }}" method="POST">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required
                                              placeholder="Motif du refus (obligatoire)"
                                              class="w-full px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-xl text-sm text-gray-700 dark:text-gray-300 border border-red-200 dark:border-red-800 focus:ring-2 focus:ring-red-300 resize-none mb-2"></textarea>
                                    <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                                        Confirmer le refus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- View on site --}}
                    @if($listing->status === 'active')
                        <a href="{{ route('listings.show', $listing) }}" target="_blank"
                           class="w-full flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-semibold py-3 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir sur le site
                        </a>
                    @endif

                    {{-- Delete --}}
                    <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST"
                          onsubmit="return confirm('Supprimer définitivement cette annonce et toutes ses images ?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="panel rounded-2xl p-5 text-xs text-gray-500 space-y-2">
                <p class="flex items-center gap-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Réf:</span>
                    DM-{{ str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                </p>
                <p class="flex items-center gap-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Créée:</span>
                    {{ $listing->created_at->format('d/m/Y H:i') }}
                </p>
                <p class="flex items-center gap-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Modifiée:</span>
                    {{ $listing->updated_at->format('d/m/Y H:i') }}
                </p>
                <p class="flex items-center gap-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Vues:</span>
                    {{ number_format($listing->views) }}
                </p>
                <p class="flex items-center gap-2">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Favoris:</span>
                    {{ $listing->favorites->count() }}
                </p>
                @if($listing->rejection_reason)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 mt-3">
                        <p class="font-semibold text-red-600 dark:text-red-400 mb-1">Motif de refus :</p>
                        <p class="text-red-500 dark:text-red-400">{{ $listing->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
