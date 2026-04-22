@extends('layouts.user')

@section('title', 'Plans & tarifs – Mon Espace')
@section('page_title', 'Plans & Tarifs')
@section('page_subtitle', 'Publiez vos annonces immobilières avec la visibilité que vous méritez. Passez à un plan supérieur à tout moment.')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
        @foreach($plans as $plan)
            @php $isCurrent = ($currentPlan === $plan->slug); @endphp
            <div class="panel rounded-[24px] overflow-hidden flex flex-col pt-0 px-0 pb-0
                        {{ $plan->priority_level >= 5 ? 'ring-2 ring-gold border-gold/50 shadow-md transform lg:-translate-y-2' : '' }}">

                @if($plan->priority_level >= 5)
                    <div class="bg-gold text-white text-center text-[10px] font-bold py-2 uppercase tracking-widest w-full">
                        Plus populaire
                    </div>
                @endif

                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="font-display text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 mb-6 min-h-[40px]">{{ $plan->slug === 'agence' ? 'Pour les agences avec volume' : ($plan->slug === 'pro' ? 'La meilleure offre pour les agents' : 'Commencez vos ventes') }}</p>

                    <div class="mb-8">
                        @if($plan->price == 0)
                            <span class="font-display text-4xl font-bold text-gray-900 dark:text-white">Gratuit</span>
                        @else
                            <span class="font-display text-4xl font-bold text-gray-900 dark:text-white">{{ $plan->price }}</span>
                            <span class="text-gray-400 dark:text-gray-500 text-sm"> MAD/mois</span>
                        @endif
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="20,6 9,17 4,12" stroke-width="2.5"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @if($isCurrent)
                        <div class="w-full py-3 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-xl text-sm font-semibold text-center mt-auto">
                            ✓ Plan actuel
                        </div>
                    @elseif($plan->price == 0 && $currentPlan !== 'gratuit')
                        <div class="w-full py-3 bg-gray-50 dark:bg-gray-800/50 text-gray-400 dark:text-gray-500 rounded-xl text-sm font-semibold text-center mt-auto">
                            Plan par défaut
                        </div>
                    @else
                        <form action="{{ route('user.checkout') }}" method="POST" class="mt-auto">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $plan->slug }}">
                            <button type="submit"
                                    class="w-full py-3 rounded-xl text-sm font-semibold transition hover:opacity-90 min-h-[48px]
                                           {{ $plan->priority_level >= 5
                                                ? 'bg-gold text-white shadow-md shadow-gold/20'
                                                : 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900' }}">
                                Choisir ce plan
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- FAQ --}}
    <div class="max-w-3xl mx-auto panel rounded-[30px] p-8 mb-6">
        <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Questions fréquentes</h2>
        <div class="space-y-3" x-data="{ open: null }">
            @foreach([
                ['q' => 'Puis-je changer de plan à tout moment ?', 'a' => 'Oui, vous pouvez passer à un plan supérieur à tout moment. Le changement est immédiat et vous êtes facturé au prorata.'],
                ['q' => 'Le paiement est-il sécurisé ?', 'a' => 'Tous les paiements sont traités de manière sécurisée par Stripe, certifié PCI-DSS. Nous ne stockons aucune information de carte bancaire.'],
                ['q' => "Qu'arrive-t-il à mes annonces si je rétrograde ?", 'a' => 'Vos annonces existantes restent actives jusqu\'à leur expiration naturelle. Seules les nouvelles annonces seront limitées au quota du nouveau plan.'],
                ['q' => 'Y a-t-il un engagement ?', 'a' => 'Non, tous les plans sont sans engagement. Vous pouvez annuler à tout moment depuis votre espace personnel.'],
            ] as $i => $faq)
                <div class="rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden bg-gray-50/50 dark:bg-gray-800/30">
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-6 py-4 text-left transition-colors hover:bg-gray-100 dark:hover:bg-gray-800">
                        <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200" :class="open === {{ $i }} ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse class="px-6 pb-4 pt-2 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
