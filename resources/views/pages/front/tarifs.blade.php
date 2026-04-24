@extends('layouts.app')

@section('title', 'Tarifs – Sarouty')
@section('description', 'Découvrez nos offres pour publier vos annonces immobilières au Maroc. Plans adaptés aux particuliers et aux agents professionnels.')

@push('styles')
<style>
    .plan-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(26, 20, 16, 0.10);
    }
    .plan-card.recommended {
        transform: translateY(-6px);
        box-shadow: 0 24px 64px rgba(200, 150, 62, 0.18);
    }
    .plan-card.recommended:hover {
        transform: translateY(-10px);
        box-shadow: 0 28px 72px rgba(200, 150, 62, 0.22);
    }
    .badge-recommended {
        background: linear-gradient(90deg, #C8963E 0%, #E8B86D 100%);
    }
    .hero-gradient {
        background: linear-gradient(135deg, #1A1410 0%, #2D2520 60%, #1A1410 100%);
    }
    .feature-check {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 2px;
        color: #C8963E;
    }
    .feature-cross {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 2px;
        color: #D1C4B8;
    }
    .tab-pill {
        cursor: pointer;
        padding: 0.5rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        color: #6B5E52;
        background: transparent;
    }
    .tab-pill.active {
        background: #1A1410;
        color: #fff;
        border-color: #1A1410;
    }
    .tab-pill:not(.active):hover {
        border-color: #C8963E;
        color: #C8963E;
    }
    .comparison-row:nth-child(even) {
        background: #F8F3EE;
    }
    .faq-item {
        border-bottom: 1px solid #EDE5DA;
    }
    .pricing-toggle {
        width: min(100%, 24rem);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }
    .comparison-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    @media (max-width: 767px) {
        .plan-card:hover,
        .plan-card.recommended,
        .plan-card.recommended:hover {
            transform: none;
        }
        .tab-pill {
            flex: 1 1 calc(50% - 0.25rem);
            text-align: center;
            padding: 0.7rem 1rem;
        }
        .comparison-table-wrap table {
            min-width: 46rem;
        }
    }
</style>
@endpush

@section('content')
<div class="pt-20 min-h-screen" style="background:#F8F3EE" x-data="{ billing: 'mensuel' }">

    {{-- ─── Hero ──────────────────────────────────────────────────────── --}}
    <div class="hero-gradient relative overflow-hidden py-14 sm:py-16 text-center">
        {{-- Decorative pattern --}}
        <div class="absolute inset-0 opacity-5" style="background-image:url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23C8963E' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"></div>

        <div class="relative max-w-3xl mx-auto px-4">
            <p class="text-gold text-xs font-semibold uppercase tracking-widest mb-4">Tarification</p>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-white mb-4 leading-tight">
                Publiez. Vendez. Réussissez.
            </h1>
            <p class="text-white/60 text-base max-w-xl mx-auto leading-relaxed">
                Des annonces gratuites pour démarrer, des plans avancés pour maximiser votre visibilité.
                Aucun engagement, résiliation à tout moment.
            </p>

            {{-- Toggle annuel/mensuel --}}
            <div class="pricing-toggle mt-8 inline-flex items-center gap-1 rounded-full bg-white/10 p-1">
                <button class="tab-pill" :class="billing === 'mensuel' ? 'active' : ''" @click="billing = 'mensuel'">Mensuel</button>
                <button class="tab-pill" :class="billing === 'annuel' ? 'active' : ''" @click="billing = 'annuel'">
                    Annuel
                    <span class="ml-1 text-xs text-gold font-semibold">-20%</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Plans ─────────────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 pb-16 overflow-hidden">

        @php
            $userRole = auth()->check() ? auth()->user()->role : null;
            $currentPlan = auth()->check() ? (auth()->user()->plan ?? 'gratuit') : null;

            $plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('priority_level')->get();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-8">
            @foreach($plans as $plan)
                @php
                    $isCurrent    = ($currentPlan === $plan->slug);
                    $isRecommended = ($plan->slug === 'starter');

                    $color = match($plan->slug) {
                        'starter' => 'gold',
                        'agence' => 'terracotta',
                        'gratuit' => 'gray',
                        default => 'ink',
                    };

                    $btnClass = match($color) {
                        'gold'      => 'bg-gold text-white hover:bg-gold-dark',
                        'terracotta'=> 'bg-terracotta text-white hover:bg-terracotta-dark',
                        default     => 'bg-ink text-white hover:bg-ink/80',
                    };
                @endphp

                <div class="plan-card bg-white rounded-2xl border overflow-hidden flex flex-col
                            {{ $isRecommended ? 'recommended border-gold' : 'border-sand-dark' }}">

                    {{-- Recommended badge --}}
                    @if($isRecommended)
                        <div class="badge-recommended text-white text-center text-xs font-bold py-2 uppercase tracking-widest">
                            Le plus populaire
                        </div>
                    @else
                        <div class="h-0"></div>
                    @endif

                    <div class="p-7 flex flex-col flex-1">

                        {{-- Plan header --}}
                        <div class="mb-6">
                            @if($color === 'gold')
                                <div class="inline-block mb-3 p-2 bg-gold/10 rounded-lg">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                            @elseif($color === 'ink')
                                <div class="inline-block mb-3 p-2 bg-ink/10 rounded-lg">
                                    <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                            @elseif($color === 'terracotta')
                                <div class="inline-block mb-3 p-2 bg-terracotta/10 rounded-lg">
                                    <svg class="w-5 h-5 text-terracotta" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            @else
                                <div class="inline-block mb-3 p-2 bg-gray-100 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif

                            <h3 class="font-display text-2xl font-bold text-ink">{{ $plan->name }}</h3>
                        </div>

                        {{-- Price --}}
                        <div class="mb-6 pb-6 border-b border-sand-dark">
                            @if($plan->price == 0)
                                <div class="font-display text-4xl font-bold text-ink">Gratuit</div>
                                <p class="text-xs text-gray-400 mt-1">Toujours gratuit</p>
                            @else
                                @php
                                    $priceM = $plan->price;
                                    $priceA = round($plan->price * 0.8, 2);
                                @endphp
                                <div>
                                    {{-- Prix mensuel --}}
                                    <div x-show="billing !== 'annuel'">
                                        <div class="flex items-end gap-1">
                                            <span class="font-display text-4xl font-bold text-ink">{{ $priceM }}</span>
                                            <span class="text-gray-400 text-sm mb-1.5">MAD / {{ $plan->duration_days ? $plan->duration_days.'j' : 'mois' }}</span>
                                        </div>
                                    </div>
                                    {{-- Prix annuel --}}
                                    <div x-show="billing === 'annuel'" style="display:none">
                                        <div class="flex items-end gap-1">
                                            <span class="font-display text-4xl font-bold text-ink">{{ $priceA }}</span>
                                            <span class="text-gray-400 text-sm mb-1.5">MAD / mensuel</span>
                                        </div>
                                        <p class="text-xs text-green-600 font-semibold mt-1">
                                            Facturé {{ number_format($priceA * 12, 0, ',', ' ') }} MAD/an — Économie de {{ number_format(($priceM - $priceA) * 12, 0, ',', ' ') }} MAD
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Features --}}
                        <ul class="space-y-3 mb-8 flex-1">
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start gap-2.5 text-sm text-gray-700">
                                        <svg class="feature-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        {{-- CTA Button --}}
                        @auth
                            @if($isCurrent)
                                <div class="w-full py-3 bg-gray-50 text-gray-400 rounded-xl text-sm font-semibold text-center border border-gray-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Votre plan actuel
                                </div>
                            @elseif($plan->price == 0)
                                <a href="{{ route('user.dashboard') }}"
                                   class="block w-full py-3 bg-gray-50 text-gray-600 rounded-xl text-sm font-semibold text-center hover:bg-gray-100 transition border border-gray-200">
                                    Tableau de bord
                                </a>
                            @else
                                <a href="{{ route('user.checkout.form', ['plan' => $plan->slug]) }}"
                                   class="w-full py-3 rounded-xl text-sm font-semibold transition-all shadow-sm {{ $btnClass }} flex items-center justify-center gap-2">
                                    Choisir ce plan
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @endif
                        @else
                            @if($plan->price == 0)
                                <a href="{{ route('register') }}"
                                   class="block w-full py-3 bg-gray-50 text-gray-600 rounded-xl text-sm font-semibold text-center hover:bg-gray-100 transition border border-gray-200">
                                    S'inscrire gratuitement
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                   class="block w-full py-3 rounded-xl text-sm font-semibold text-center transition-all shadow-sm {{ $btnClass }} flex items-center justify-center gap-2">
                                    Commencer maintenant
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ─── Garanties ──────────────────────────────────────────────── --}}
        <div class="mt-12 rounded-2xl border border-sand-dark bg-white p-6 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="p-3 bg-green-50 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-ink text-sm">Paiement sécurisé</p>
                        <p class="text-xs text-gray-400 mt-1">Transactions chiffrées via Stripe, certifié PCI-DSS</p>
                    </div>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="p-3 bg-blue-50 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-ink text-sm">Sans engagement</p>
                        <p class="text-xs text-gray-400 mt-1">Annulez ou changez de plan à tout moment</p>
                    </div>
                </div>
                <div class="flex flex-col items-center gap-3">
                    <div class="p-3 bg-gold/10 rounded-xl">
                        <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-ink text-sm">Support réactif</p>
                        <p class="text-xs text-gray-400 mt-1">Réponse sous 24h pour tous les plans</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Tableau comparatif ─────────────────────────────────────── --}}
        <div class="mt-16">
            <h2 class="font-display text-3xl font-bold text-ink text-center mb-2">Comparaison détaillée</h2>
            <p class="text-gray-400 text-sm text-center mb-10">Toutes les fonctionnalités en un coup d'oeil</p>

            @php
                $rows = [
                    ['label' => 'Annonces / mois (particulier)',  'vals' => ['2', '10', '20', 'Illimit.']],
                    ['label' => 'Annonces / mois (agent)',        'vals' => ['5', '15', '30', 'Illimit.']],
                    ['label' => 'Photos par annonce',             'vals' => ['5', '15', '20', 'Illimitees']],
                    ['label' => 'Duree de visibilite',            'vals' => ['30 jours', '60 jours', '90 jours', '120 jours']],
                    ['label' => 'Niveau de priorite',             'vals' => ['0', '2', '5', '8']],
                    ['label' => 'Mise en avant',                  'vals' => [false, true, true, true]],
                    ['label' => 'Badge verifie',                  'vals' => [false, false, true, true]],
                    ['label' => 'Statistiques',                   'vals' => ['Basiques', 'Detaillees', 'Avancees', 'Completes']],
                    ['label' => 'Support',                        'vals' => ['Email', 'Prioritaire', 'Telephone', 'Account manager']],
                    ['label' => 'Export CSV',                     'vals' => [false, false, false, true]],
                    ['label' => 'Acces API',                      'vals' => [false, false, false, true]],
                ];
                $comparisonPlans = [
                    ['name' => 'Gratuit', 'index' => 0, 'accent' => 'text-gray-800 border-gray-200'],
                    ['name' => 'Starter', 'index' => 1, 'accent' => 'text-gold border-gold/30 bg-gold/5'],
                    ['name' => 'Pro', 'index' => 2, 'accent' => 'text-ink border-ink/10'],
                    ['name' => 'Agence', 'index' => 3, 'accent' => 'text-terracotta border-terracotta/20'],
                ];
            @endphp

            <div class="grid grid-cols-1 gap-4 md:hidden">
                @foreach($comparisonPlans as $planCard)
                    <div class="rounded-2xl border p-4 shadow-sm {{ $planCard['accent'] }}">
                        <h3 class="font-display text-2xl font-bold {{ str_contains($planCard['accent'], 'text-gold') ? 'text-gold' : (str_contains($planCard['accent'], 'text-terracotta') ? 'text-terracotta' : 'text-ink') }}">
                            {{ $planCard['name'] }}
                        </h3>
                        <div class="mt-4 space-y-3">
                            @foreach($rows as $row)
                                @php $val = $row['vals'][$planCard['index']]; @endphp
                                <div class="rounded-xl bg-white/80 px-3 py-3">
                                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">{{ $row['label'] }}</div>
                                    <div class="mt-2 text-sm font-medium text-gray-700">
                                        @if($val === true)
                                            <span class="inline-flex items-center gap-2 text-gold">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Inclus
                                            </span>
                                        @elseif($val === false)
                                            <span class="inline-flex items-center gap-2 text-gray-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Non inclus
                                            </span>
                                        @else
                                            {{ $val }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="comparison-table-wrap hidden overflow-hidden rounded-2xl border border-sand-dark bg-white shadow-sm md:block">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-sand-dark">
                            <th class="w-1/3 p-5 text-left font-medium text-gray-500">Fonctionnalité</th>
                            <th class="p-5 text-center font-bold text-gray-800">Gratuit</th>
                            <th class="bg-gold/5 p-5 text-center font-bold text-gold">Starter</th>
                            <th class="p-5 text-center font-bold text-ink">Pro</th>
                            <th class="p-5 text-center font-bold text-terracotta">Agence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr class="comparison-row border-b border-sand-dark last:border-0">
                                <td class="p-5 font-medium text-gray-700">{{ $row['label'] }}</td>
                                @foreach($row['vals'] as $val)
                                    <td class="p-5 text-center {{ $loop->index === 1 ? 'bg-gold/5' : '' }}">
                                        @if($val === true)
                                            <svg class="mx-auto h-5 w-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($val === false)
                                            <svg class="mx-auto h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @else
                                            <span class="font-medium text-gray-700">{{ $val }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─── FAQ ───────────────────────────────────────────────────── --}}
        <div class="mt-16 max-w-2xl mx-auto">
            <h2 class="font-display text-3xl font-bold text-ink text-center mb-2">Questions fréquentes</h2>
            <p class="text-gray-400 text-sm text-center mb-10">Toutes les réponses à vos questions</p>

            <div class="divide-y divide-sand-dark" x-data="{ open: null }">
                @php
                    $faqs = [
                        [
                            'q' => 'Quelle est la différence entre particulier et agent ?',
                            'a' => 'Un particulier dispose de 3 annonces gratuites actives simultanément. Un agent immobilier bénéficie de 5 annonces gratuites. Au-delà de ces quotas, un abonnement payant est nécessaire pour publier davantage.',
                        ],
                        [
                            'q' => 'Puis-je changer de plan à tout moment ?',
                            'a' => 'Oui, vous pouvez passer à un plan supérieur ou inférieur à tout moment depuis votre espace personnel. Le changement est immédiat.',
                        ],
                        [
                            'q' => 'Que se passe-t-il si je dépasse mon quota gratuit ?',
                            'a' => 'Vos annonces existantes restent actives. Vous serez simplement invité à souscrire un plan payant pour publier de nouvelles annonces supplémentaires.',
                        ],
                        [
                            'q' => 'Le paiement est-il sécurisé ?',
                            'a' => 'Tous les paiements sont traités de manière sécurisée par Stripe, certifié PCI-DSS niveau 1. Nous ne stockons aucune information bancaire sur nos serveurs.',
                        ],
                        [
                            'q' => 'Y a-t-il un engagement de durée ?',
                            'a' => 'Non. Tous les plans sont sans engagement. Vous pouvez annuler votre abonnement à tout moment depuis votre tableau de bord, sans frais.',
                        ],
                        [
                            'q' => 'Comment fonctionne la réduction annuelle ?',
                            'a' => 'En choisissant la facturation annuelle, vous bénéficiez de 20% de réduction sur le tarif mensuel. Le montant total est facturé en une seule fois au début de la période.',
                        ],
                    ];
                @endphp

                @foreach($faqs as $i => $faq)
                    <div class="faq-item py-5">
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between text-left gap-4">
                            <span class="font-medium text-ink text-sm leading-relaxed">{{ $faq['q'] }}</span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                 :class="open === {{ $i }} ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-3 text-sm text-gray-500 leading-relaxed pr-8">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ─── CTA final ──────────────────────────────────────────────── --}}
        <div class="mt-16 relative overflow-hidden rounded-2xl bg-ink p-6 sm:p-10 text-center">
            <div class="absolute inset-0 opacity-5" style="background-image:url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23C8963E' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"></div>
            <div class="relative">
                <p class="text-gold text-xs font-semibold uppercase tracking-widest mb-3">Besoin d'aide ?</p>
                <h3 class="font-display text-3xl font-bold text-white mb-3">Une question sur nos offres ?</h3>
                <p class="text-white/50 text-sm max-w-md mx-auto mb-8">
                    Notre équipe est disponible pour vous accompagner dans le choix du plan le mieux adapté à votre situation.
                </p>
                <a href="mailto:contact@sarouty.ma"
                   class="inline-flex items-center gap-2 bg-gold text-white font-semibold text-sm px-8 py-3 rounded-xl hover:bg-gold-dark transition-all shadow-lg shadow-gold/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contacter notre equipe
                </a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Sync billing toggle across all x-data scopes
    document.addEventListener('alpine:init', () => {
        Alpine.store('billing', 'mensuel');
    });
</script>
@endpush

@endsection
