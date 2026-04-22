@extends('layouts.admin')

@section('title', ($payment->exists ? 'Modifier' : 'Créer') . ' un abonnement – Sarouty')
@section('page_title', $payment->exists ? 'Modifier l\'abonnement' : 'Créer un abonnement')
@section('page_subtitle', $payment->exists ? '#' . $payment->id . ' - ' . $payment->plan_label : 'Attribuer un plan à un utilisateur')

@section('top_actions')
    @if($payment->exists)
        <a href="{{ route('admin.subscriptions.show', $payment) }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2.5 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Voir les détails
        </a>
    @endif
@endsection

@section('content')
    <form action="{{ $payment->exists ? route('admin.subscriptions.update', $payment) : route('admin.subscriptions.store') }}" method="POST">
        @csrf
        @if($payment->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- User Selection (create only) --}}
                @if(!$payment->exists)
                    <div class="panel rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Utilisateur
                        </h3>

                        @if($selectedUser)
                            <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border-2 border-gold/30">
                                <div class="w-14 h-14 rounded-full bg-gold/10 flex items-center justify-center">
                                    @if($selectedUser->avatar)
                                        <img src="{{ $selectedUser->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-gold font-bold text-lg">{{ strtoupper(substr($selectedUser->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $selectedUser->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $selectedUser->email }}</div>
                                    <div class="text-xs text-gray-400 mt-1">Plan actuel: {{ $selectedUser->plan_label }}</div>
                                </div>
                                <a href="{{ route('admin.subscriptions.create') }}" class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sélectionner un utilisateur</label>
                                <select name="user_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                                    <option value="">Choisir...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }}) - {{ $user->plan_label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="hidden" name="user_id" value="{{ $payment->user_id }}">
                @endif

                {{-- Plan Selection --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Plan
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($plans as $planKey => $planData)
                            @if($planKey !== 'gratuit')
                                <label class="relative block p-4 rounded-xl border-2 cursor-pointer transition
                                             {{ old('plan', $payment->plan ?? '') === $planKey ? 'border-gold bg-gold/5' : 'border-gray-200 dark:border-gray-700 hover:border-gold/40' }}">
                                    <input type="radio" name="plan" value="{{ $planKey }}" 
                                           class="sr-only" 
                                           {{ old('plan', $payment->plan ?? '') === $planKey ? 'checked' : '' }}
                                           onchange="updatePlanDetails('{{ $planKey }}', {{ $planData['price'] }})">
                                    <div class="text-center">
                                        <div class="font-semibold text-gray-900 dark:text-white mb-1">{{ $planData['name'] }}</div>
                                        <div class="text-2xl font-bold text-gold mb-1">{{ $planData['price'] }} MAD</div>
                                        <div class="text-xs text-gray-500">/mois</div>
                                    </div>
                                    @if(old('plan', $payment->plan ?? '') === $planKey)
                                        <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-gold flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @endif
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Duration & Pricing --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Durée et tarification</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if(!$payment->exists)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durée (jours)</label>
                                <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', 30) }}" min="1" max="365"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                       onchange="calculateTotal()">
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant (MAD)</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $payment->amount ?? 0) }}" min="0" step="0.01"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                            <p class="text-xs text-gray-400 mt-1">Laissez vide pour utiliser le prix par défaut</p>
                        </div>
                    </div>

                    @if($payment->exists)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'expiration</label>
                            <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $payment->expires_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>
                    @endif
                </div>

                {{-- Status (edit only) --}}
                @if($payment->exists)
                    <div class="panel rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statut</h3>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                            @foreach(\App\Models\Payment::STATUSES as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $payment->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Summary --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Récapitulatif</h3>
                    <div class="space-y-3 text-sm">
                        @if(!$payment->exists)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Durée</span>
                                <span class="font-medium text-gray-900 dark:text-white"><span id="summary-days">30</span> jours</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Plan</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="summary-plan">{{ $plans[$payment->plan ?? 'starter']['name'] ?? 'Starter' }}</span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="font-bold text-gold" id="summary-total">{{ number_format($payment->amount ?? 99, 0, ',', ' ') }} MAD</span>
                        </div>
                    </div>
                </div>

                {{-- Features --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Avantages inclus</h3>
                    <ul class="space-y-2" id="plan-features">
                        @php
                            $defaultPlan = $payment->plan ?? 'starter';
                        @endphp
                        @foreach(\App\Models\User::PLANS[$defaultPlan]['features'] ?? [] as $feature)
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="20,6 9,17 4,12" stroke-width="2.5"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Actions --}}
                <div class="panel rounded-2xl p-6">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-gold text-white font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $payment->exists ? 'Enregistrer les modifications' : 'Créer l\'abonnement' }}
                    </button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="block text-center py-2.5 mt-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const plans = @json($plans);
    const planFeatures = @json(collect(\App\Models\User::PLANS)->mapWithKeys(fn($v, $k) => [$k => $v['features']]));
    
    function updatePlanDetails(planKey, price) {
        document.getElementById('amount').value = price;
        document.getElementById('summary-plan').textContent = plans[planKey].name;
        
        const days = parseInt(document.getElementById('duration_days')?.value || 30);
        const total = price;
        document.getElementById('summary-total').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' MAD';
        
        // Update features
        const featuresList = document.getElementById('plan-features');
        const features = planFeatures[planKey] || [];
        featuresList.innerHTML = features.map(f => `
            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12" stroke-width="2.5"/>
                </svg>
                ${f}
            </li>
        `).join('');
    }
    
    function calculateTotal() {
        const days = parseInt(document.getElementById('duration_days')?.value || 30);
        const selectedPlan = document.querySelector('input[name="plan"]:checked');
        const pricePerMonth = selectedPlan ? plans[selectedPlan.value].price : 99;
        const total = pricePerMonth;
        
        document.getElementById('summary-days').textContent = days;
        document.getElementById('summary-total').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' MAD';
    }
</script>
@endpush
