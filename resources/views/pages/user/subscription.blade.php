@extends('layouts.user')

@section('title', 'Mon Abonnement – Mon Espace')
@section('page_title', 'Mon Abonnement')
@section('page_subtitle', 'Consultez les details de votre plan actuel et votre historique')

@section('top_actions')
    <a href="{{ route('tarifs') }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"></path>
        </svg>
        Voir tous les tarifs
    </a>
@endsection

@section('content')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $usedPct = $quota > 0 ? min(100, ($listingsUsed / $quota) * 100) : 100;
            $sponsoredCount = $sponsoredListings->count();
        @endphp
        @foreach([
            ['label' => 'Plan actuel', 'value' => ucfirst($currentPlan), 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6', 'bg' => 'from-gold to-gold-dark'],
            ['label' => 'Quota utilise', 'value' => "{$listingsUsed}/{$quota}", 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'bg' => 'from-blue-500 to-blue-600'],
            ['label' => 'Jours restants', 'value' => $daysRemaining ?? 'Illimite', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'from-emerald-500 to-emerald-600'],
            ['label' => 'Annonces sponsorisees', 'value' => $sponsoredCount, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'bg' => 'from-amber-500 to-amber-600'],
        ] as $stat)
            <div class="panel rounded-2xl p-5 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $stat['bg'] }} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $stat['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Colonne principale (2/3) --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Carte du plan actuel --}}
                <div class="panel rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"></path>
                                </svg>
                            </div>
                            <h2 class="font-semibold text-gray-900 dark:text-white text-lg">Plan {{ ucfirst($currentPlan) }}</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $currentPlan === 'gratuit' ? 'bg-gray-100 text-gray-700' : ($currentPlan === 'starter' ? 'bg-blue-100 text-blue-700' : ($currentPlan === 'pro' ? 'bg-purple-100 text-purple-700' : 'bg-gold/20 text-gold-dark')) }}">
                            {{ ucfirst($currentPlan) }}
                        </span>
                    </div>

                    <div class="p-6">

                    {{-- Details du plan --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Quota mensuel</p>
                            <p class="text-2xl font-bold text-ink">{{ $quota }} <span class="text-sm font-normal text-gray-500">annonces</span></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Photos par annonce</p>
                            <p class="text-2xl font-bold text-ink">{{ $planData->{'max_images_' . ($user->role === 'agent' ? 'agent' : 'particulier')} ?? 5 }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Prix</p>
                            <p class="text-2xl font-bold text-ink">{{ $planData->price ?? 0 }} <span class="text-sm font-normal text-gray-500">MAD/mois</span></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priorite</p>
                            <p class="text-2xl font-bold text-ink">Niveau {{ $planData->priority_level ?? 0 }}</p>
                        </div>
                    </div>

                    {{-- Expiration --}}
                    @if($expiresAt)
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">Expiration dans {{ $daysRemaining }} jours</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-300">{{ $expiresAt->format('d/m/Y a H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-green-800 dark:text-green-200">Plan gratuit permanent</p>
                                    <p class="text-xs text-green-700 dark:text-green-300">Pas de date d'expiration</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Fonctionnalites --}}
                    <h3 class="font-semibold text-ink mb-3">Fonctionnalites incluses:</h3>
                    <ul class="space-y-2">
                        @foreach($planData->features ?? [] as $feature)
                            <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @if($currentPlan !== 'gratuit')
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('tarifs') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium border-2 border-gold text-gold hover:bg-gold hover:text-white transition-colors">
                                Changer de plan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                    </div>
                </div>

                {{-- Historique des paiements --}}
                <div class="panel rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h2 class="font-semibold text-gray-900 dark:text-white text-lg">Historique des paiements</h2>
                        </div>
                    </div>

                    <div class="p-6">
                    @if($paymentHistory->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun paiement effectue.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-2 px-3 text-gray-500 dark:text-gray-400">Date</th>
                                        <th class="text-left py-2 px-3 text-gray-500 dark:text-gray-400">Plan</th>
                                        <th class="text-left py-2 px-3 text-gray-500 dark:text-gray-400">Montant</th>
                                        <th class="text-left py-2 px-3 text-gray-500 dark:text-gray-400">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentHistory as $payment)
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="py-2 px-3">{{ $payment->created_at->format('d/m/Y') }}</td>
                                            <td class="py-2 px-3 capitalize">{{ $payment->plan }}</td>
                                            <td class="py-2 px-3 font-semibold">{{ $payment->amount }} MAD</td>
                                            <td class="py-2 px-3">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            {{-- Colonne laterale (1/3) --}}
            <div class="space-y-6">
                {{-- Utilisation du quota --}}
                <div class="panel rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Quota mensuel</h3>
                        </div>
                    </div>

                    <div class="p-6">
                    @php
                        $usedPct = $quota > 0 ? min(100, ($listingsUsed / $quota) * 100) : 100;
                    @endphp
                    <div class="mb-4">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-3xl font-bold text-ink">{{ $listingsUsed }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">sur {{ $quota }} annonces</span>
                        </div>
                        <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700"
                                 style="width:{{ $usedPct }}%; background:{{ $usedPct >= 90 ? 'linear-gradient(to right, #EF4444, #DC2626)' : ($usedPct >= 70 ? 'linear-gradient(to right, #F59E0B, #D97706)' : 'linear-gradient(to right, #C8963E, #9B6E22)') }}">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $quota - $listingsUsed }} annonce(s) restante(s) ce mois-ci
                        </p>
                    </div>
                    </div>
                </div>

                {{-- Annonces sponsorisees --}}
                <div class="panel rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Annonces sponsorisees</h3>
                        </div>
                    </div>

                    <div class="p-6">
                    @if($sponsoredListings->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucune annonce sponsorisee active.</p>
                        <a href="{{ route('user.dashboard') }}" class="mt-4 inline-flex items-center gap-2 text-sm text-gold hover:text-gold-dark">
                            Gerer mes annonces
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    @else
                        <div class="space-y-3">
                            @foreach($sponsoredListings as $listing)
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                                    <p class="text-sm font-medium text-ink truncate">{{ $listing->title }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        Expire dans {{ $listing->sponsored_remaining_days }} jours
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                </div>

                {{-- Plans disponibles --}}
                <div class="panel rounded-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Plans disponibles</h3>
                        </div>
                    </div>

                    <div class="p-6">
                    <div class="space-y-3">
                        @foreach($allPlans as $planSlug => $plan)
                            @if($planSlug !== $currentPlan)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-ink">{{ $plan->name }}</span>
                                        <span class="text-sm font-bold text-gold">{{ $plan->price }} MAD</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        {{ $plan->{'max_ads_' . ($user->role === 'agent' ? 'agent' : 'particulier')} ?? 2 }} annonces/mois
                                    </p>
                                    <a href="{{ route('user.checkout.form', ['plan' => $planSlug]) }}" class="block text-center py-2 rounded-lg text-xs font-semibold bg-gold text-white hover:bg-gold-dark transition-colors">
                                        Passer a ce plan
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <a href="{{ route('tarifs') }}" class="mt-4 block text-center text-sm text-gold hover:text-gold-dark font-medium">
                        Voir tous les plans et tarifs
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
