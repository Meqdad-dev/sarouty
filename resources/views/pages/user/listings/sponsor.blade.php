@extends('layouts.user')

@section('title', 'Sponsoriser – ' . $listing->title)
@section('page_title', 'Sponsoriser une annonce')
@section('page_subtitle', Str::limit($listing->title, 60))

@section('top_actions')
    <a href="{{ route('user.listings.show', $listing) }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- Listing summary --}}
    <div class="panel rounded-[24px] p-6 flex items-center gap-5">
        <div class="w-20 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-gray-700">
            @if($listing->thumbnail_url)
                <img src="{{ $listing->thumbnail_url }}" alt="" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $listing->title }}</h3>
            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 mt-1">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    {{ $listing->city }}
                </span>
                <span class="font-semibold text-gold">{{ $listing->formatted_price }}</span>
            </div>
        </div>
    </div>

    {{-- How it works --}}
    <div class="panel rounded-[24px] p-6">
        <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-4">Comment fonctionne la sponsorisation ?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => '1. Choisir une formule', 'desc' => 'Selectionnez la duree qui correspond a vos besoins.'],
                ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => '2. Payer en ligne', 'desc' => 'Reglez par carte bancaire de maniere securisee via Stripe.'],
                ['icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'title' => '3. Visibilite accrue', 'desc' => 'Votre annonce remonte dans les resultats immediatement.'],
            ] as $step)
                <div class="text-center">
                    <div class="w-12 h-12 rounded-xl bg-gold/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">{{ $step['title'] }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Sponsorship options --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($types as $key => $type)
            @php
                $isPopular = ($key === 'premium');
                $colorMap = [
                    'basic' => ['border' => 'border-gray-200 dark:border-gray-700', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'btn' => 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:opacity-90'],
                    'premium' => ['border' => 'border-gold', 'bg' => 'bg-gold/5', 'btn' => 'bg-gold hover:bg-gold-dark text-white'],
                    'premium_plus' => ['border' => 'border-gray-200 dark:border-gray-700', 'bg' => 'bg-gray-50 dark:bg-gray-800/50', 'btn' => 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:opacity-90'],
                ];
                $colors = $colorMap[$key] ?? $colorMap['basic'];
            @endphp

            <div class="panel rounded-[24px] {{ $colors['border'] }} flex flex-col relative overflow-hidden {{ $isPopular ? 'ring-2 ring-gold/40 shadow-lg shadow-gold/10' : '' }}">
                @if($isPopular)
                    <div class="text-center text-xs font-bold uppercase tracking-widest text-white py-2" style="background: linear-gradient(90deg, #C8963E, #E8B86D)">
                        Le plus populaire
                    </div>
                @endif

                <div class="p-6 flex flex-col flex-1">
                    <div class="mb-5">
                        <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white">{{ $type['label'] }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $type['days'] }} jours de visibilite</p>
                    </div>

                    <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-end gap-1">
                            <span class="font-display text-4xl font-bold text-gray-900 dark:text-white">{{ $type['price'] }}</span>
                            <span class="text-gray-400 text-sm mb-1.5">MAD</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($type['price'] / $type['days'], 0) }} MAD / jour</p>
                    </div>

                    <ul class="space-y-3 mb-6 flex-1">
                        @php
                            $features = [
                                'basic' => [
                                    'Remontee dans les resultats',
                                    'Badge "Sponsorisee" sur la carte',
                                    'Visibilite pendant 7 jours',
                                ],
                                'premium' => [
                                    'Remontee prioritaire dans les resultats',
                                    'Badge "Sponsorisee" sur la carte',
                                    'Visibilite pendant 14 jours',
                                    'Boost de priorite renforce',
                                ],
                                'premium_plus' => [
                                    'Remontee maximale dans les resultats',
                                    'Badge "Sponsorisee" sur la carte',
                                    'Visibilite pendant 30 jours',
                                    'Boost de priorite maximal',
                                    'Meilleur rapport qualite/prix',
                                ],
                            ];
                        @endphp
                        @foreach($features[$key] ?? [] as $feature)
                            <li class="flex items-start gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('user.listings.sponsor.checkout.form', ['listing' => $listing, 'type' => $key]) }}"
                       class="w-full py-3 rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center justify-center gap-2 {{ $colors['btn'] }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Sponsoriser {{ $type['days'] }}j - {{ $type['price'] }} MAD
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Guarantees --}}
    <div class="panel rounded-[24px] p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="flex flex-col items-center gap-2">
                <div class="p-2.5 bg-green-50 dark:bg-green-900/20 rounded-xl">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Paiement securise</p>
                <p class="text-xs text-gray-400">Transactions chiffrees via Stripe</p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Activation instantanee</p>
                <p class="text-xs text-gray-400">Boost actif des le paiement</p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="p-2.5 bg-gold/10 rounded-xl">
                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">Sans engagement</p>
                <p class="text-xs text-gray-400">Paiement unique, pas d'abonnement</p>
            </div>
        </div>
    </div>
</div>
@endsection
