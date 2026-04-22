@extends('layouts.user')

@section('title', 'Paiement – Sarouty')
@section('page_title', 'Finaliser le paiement')
@section('page_subtitle', $type === 'subscription' ? 'Abonnement ' . $plan->name : 'Sponsorisation ' . $sponsorshipType['label'])

@section('top_actions')
    @if($type === 'subscription')
        <a href="{{ route('tarifs') }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour aux tarifs
        </a>
    @else
        <a href="{{ route('user.listings.sponsor', $listing) }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
    @endif
@endsection

@section('content')
<div class="max-w-3xl mx-auto" x-data="checkoutForm()">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Payment form (3/5) --}}
        <div class="lg:col-span-3">
            <div class="panel rounded-[24px] p-6 lg:p-8">

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white">Informations de paiement</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Toutes les transactions sont securisees</p>
                    </div>
                </div>

                @if($type === 'subscription')
                    <form method="POST" action="{{ route('user.checkout') }}" @submit="processing = true" id="payment-form">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $planSlug }}">
                @else
                    <form method="POST" action="{{ route('user.listings.sponsor.checkout', $listing) }}" @submit="processing = true" id="payment-form">
                        @csrf
                        <input type="hidden" name="type" value="{{ $sponsorshipKey }}">
                @endif

                    {{-- Cardholder name --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom du titulaire de la carte</label>
                        <input type="text" name="cardholder_name" required
                               value="{{ old('cardholder_name', $user->name) }}"
                               placeholder="Nom complet"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold transition placeholder:text-gray-400">
                    </div>

                    {{-- Card number --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Numero de carte</label>
                        <div class="relative">
                            <input type="text" name="card_number" required
                                   x-model="cardNumber"
                                   @input="formatCardNumber"
                                   maxlength="19"
                                   placeholder="1234 5678 9012 3456"
                                   class="w-full px-4 py-3 pr-14 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold transition placeholder:text-gray-400 font-mono tracking-wider">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                                <svg class="w-8 h-5 text-gray-300" viewBox="0 0 48 32" fill="currentColor"><rect width="48" height="32" rx="4"/><circle cx="18" cy="16" r="7" fill="#EB001B" opacity="0.8"/><circle cx="30" cy="16" r="7" fill="#F79E1B" opacity="0.8"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Expiry + CVV --}}
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'expiration</label>
                            <input type="text" name="card_expiry" required
                                   x-model="cardExpiry"
                                   @input="formatExpiry"
                                   maxlength="5"
                                   placeholder="MM/AA"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold transition placeholder:text-gray-400 font-mono tracking-wider">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Code de securite (CVV)</label>
                            <input type="text" name="card_cvv" required
                                   x-model="cardCvv"
                                   @input="cardCvv = cardCvv.replace(/\D/g, '').substring(0, 4)"
                                   maxlength="4"
                                   placeholder="123"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold transition placeholder:text-gray-400 font-mono tracking-wider">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email de confirmation</label>
                        <input type="email" name="email" required
                               value="{{ old('email', $user->email) }}"
                               placeholder="votre@email.com"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-gold/30 focus:border-gold transition placeholder:text-gray-400">
                    </div>

                    {{-- Security notice --}}
                    <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/30 mb-6">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-400">Paiement securise</p>
                            <p class="text-xs text-green-600 dark:text-green-500 mt-0.5">Vos donnees sont chiffrees et traitees de maniere securisee. Aucune information bancaire n'est stockee sur nos serveurs.</p>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            :disabled="processing"
                            class="w-full py-3.5 bg-gold hover:bg-gold-dark text-white font-semibold rounded-xl text-sm transition-all shadow-lg shadow-gold/20 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!processing">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Payer {{ $type === 'subscription' ? $plan->price : $sponsorshipType['price'] }} MAD
                            </span>
                        </template>
                        <template x-if="processing">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Traitement en cours...
                            </span>
                        </template>
                    </button>
                </form>
            </div>
        </div>

        {{-- Order summary (2/5) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Summary card --}}
            <div class="panel rounded-[24px] p-6">
                <h3 class="font-display font-semibold text-gray-900 dark:text-white mb-5">Recapitulatif</h3>

                @if($type === 'subscription')
                    <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                        @php
                            $colorMap = ['starter' => 'gold', 'agence' => 'terracotta', 'gratuit' => 'gray'];
                            $color = $colorMap[$plan->slug] ?? 'ink';
                            $iconColorMap = [
                                'gold' => 'bg-gold/10 text-gold',
                                'ink' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300',
                                'terracotta' => 'bg-orange-50 dark:bg-orange-900/20 text-orange-600',
                            ];
                            $iconColor = $iconColorMap[$color] ?? 'bg-gray-100 text-gray-500';
                        @endphp
                        <div class="w-12 h-12 rounded-xl {{ $iconColor }} flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Plan {{ $plan->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->duration_days ?? 30 }} jours</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                        @foreach(array_slice($plan->features ?? [], 0, 4) as $feature)
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $feature }}
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Sponsorship summary --}}
                    <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Sponsorisation {{ $sponsorshipType['label'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sponsorshipType['days'] }} jours</p>
                        </div>
                    </div>

                    @if($listing)
                        <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                            <div class="w-14 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-gray-700">
                                <img src="{{ $listing->thumbnail_url }}" alt="" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $listing->title }}</p>
                                <p class="text-xs text-gray-500">{{ $listing->city }} - {{ $listing->formatted_price }}</p>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Pricing breakdown --}}
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400">
                            @if($type === 'subscription')
                                Plan {{ $plan->name }}
                            @else
                                Sponsorisation {{ $sponsorshipType['label'] }}
                            @endif
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $type === 'subscription' ? $plan->price : $sponsorshipType['price'] }} MAD
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-gray-400">
                        <span>Taxes incluses</span>
                        <span>0 MAD</span>
                    </div>
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-3 mt-3">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="font-display text-2xl font-bold text-gold">
                                {{ $type === 'subscription' ? $plan->price : $sponsorshipType['price'] }} MAD
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Trust badges --}}
            <div class="panel rounded-[24px] p-5">
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'text-green-600', 'text' => 'Chiffrement SSL 256 bits'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'text-blue-600', 'text' => 'Certifie PCI-DSS'],
                        ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color' => 'text-gold', 'text' => 'Activation immediate'],
                    ] as $badge)
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ $badge['color'] }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $badge['icon'] }}"/></svg>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $badge['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function checkoutForm() {
    return {
        processing: false,
        cardNumber: '',
        cardExpiry: '',
        cardCvv: '',

        formatCardNumber() {
            let value = this.cardNumber.replace(/\D/g, '').substring(0, 16);
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            this.cardNumber = formatted;
        },

        formatExpiry() {
            let value = this.cardExpiry.replace(/\D/g, '').substring(0, 4);
            if (value.length >= 2) {
                this.cardExpiry = value.substring(0, 2) + '/' + value.substring(2);
            } else {
                this.cardExpiry = value;
            }
        }
    }
}
</script>
@endpush
@endsection
