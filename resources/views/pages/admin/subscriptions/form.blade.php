@extends('layouts.admin')

@section('title', ($payment->exists ? 'Modifier' : 'Créer') . ' un abonnement – Sarouty')
@section('page_title', $payment->exists ? 'Modifier l\'abonnement' : 'Créer un abonnement')
@section('page_subtitle', $payment->exists ? '#' . $payment->id . ' - ' . $payment->plan_label : 'Attribuer un plan à un utilisateur avec un style visuel cohérent')

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
            <div class="xl:col-span-2 space-y-6">
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

                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Plan
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($plans as $planKey => $plan)
                            @if($planKey !== 'gratuit')
                                @php $theme = $plan->theme_preset; $checked = old('plan', $payment->plan ?? '') === $planKey; @endphp
                                <label class="relative block p-4 rounded-[22px] border-2 cursor-pointer transition shadow-sm"
                                       style="background: linear-gradient(135deg, {{ $theme['soft'] }}, #fff); border-color: {{ $checked ? $theme['hex'] : $theme['border'] }}; box-shadow: 0 18px 35px {{ $checked ? $theme['glow'] : 'rgba(15, 23, 42, 0.04)' }};">
                                    <input type="radio" name="plan" value="{{ $planKey }}" class="sr-only" {{ $checked ? 'checked' : '' }} onchange="updatePlanDetails('{{ $planKey }}')">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[11px] font-semibold" style="background: #fff; color: {{ $theme['text'] }}; border: 1px solid {{ $theme['border'] }};">
                                                <span class="w-2 h-2 rounded-full" style="background: {{ $theme['hex'] }};"></span>
                                                {{ $plan->theme_name }}
                                            </div>
                                            <div class="mt-3 font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</div>
                                            <div class="text-2xl font-black mt-2" style="color: {{ $theme['button'] }};">{{ number_format($plan->price, 0, ',', ' ') }} MAD</div>
                                            <div class="text-xs text-gray-500 mt-1">/{{ $plan->duration_days ?: 30 }} jours</div>
                                        </div>
                                        @if($checked)
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white" style="background: {{ $theme['button'] }};">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

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
                            <p class="text-xs text-gray-400 mt-1">Laissez vide pour utiliser le prix du plan sélectionné.</p>
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

            <div class="space-y-6">
                @php
                    $defaultKey = old('plan', $payment->plan ?? $plans->keys()->first());
                    $defaultPlan = $plans[$defaultKey] ?? $plans->first();
                    $defaultTheme = $defaultPlan?->theme_preset;
                @endphp
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Récapitulatif</h3>
                    <div id="summary-card" class="rounded-[24px] border p-5" style="background: linear-gradient(135deg, {{ $defaultTheme['soft'] ?? '#F8FAFC' }}, #fff); border-color: {{ $defaultTheme['border'] ?? '#CBD5E1' }};">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs uppercase tracking-[0.22em] text-gray-400">Plan choisi</div>
                                <div class="mt-2 text-xl font-bold" id="summary-plan" style="color: {{ $defaultTheme['text'] ?? '#475569' }};">{{ $defaultPlan->name ?? 'Plan' }}</div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl border-4 border-white shadow-lg" id="summary-swatch" style="background: linear-gradient(135deg, {{ $defaultTheme['hex'] ?? '#94A3B8' }}, {{ $defaultTheme['button_hover'] ?? '#1E293B' }});"></div>
                        </div>
                        <div class="space-y-3 text-sm mt-5">
                            @if(!$payment->exists)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Durée</span>
                                    <span class="font-medium text-gray-900 dark:text-white"><span id="summary-days">30</span> jours</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg">
                                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                                <span class="font-bold" id="summary-total" style="color: {{ $defaultTheme['button'] ?? '#334155' }};">{{ number_format($payment->amount ?? ($defaultPlan->price ?? 0), 0, ',', ' ') }} MAD</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Avantages inclus</h3>
                    <ul class="space-y-2" id="plan-features">
                        @foreach($defaultPlan?->features ?? [] as $feature)
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 flex-shrink-0" style="color: {{ $defaultTheme['button'] ?? '#334155' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="20,6 9,17 4,12" stroke-width="2.5"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>

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
    const plans = @json($plans->map(fn($plan) => [
        'name' => $plan->name,
        'price' => (float) $plan->price,
        'features' => $plan->features ?? [],
        'theme' => $plan->theme_preset,
    ]));

    function updatePlanDetails(planKey) {
        const plan = plans[planKey];
        if (!plan) return;

        document.getElementById('amount').value = plan.price;
        document.getElementById('summary-plan').textContent = plan.name;
        document.getElementById('summary-total').textContent = new Intl.NumberFormat('fr-FR').format(plan.price) + ' MAD';
        document.getElementById('summary-total').style.color = plan.theme.button;
        document.getElementById('summary-swatch').style.background = `linear-gradient(135deg, ${plan.theme.hex}, ${plan.theme.button_hover})`;
        document.getElementById('summary-card').style.background = `linear-gradient(135deg, ${plan.theme.soft}, #fff)`;
        document.getElementById('summary-card').style.borderColor = plan.theme.border;
        document.getElementById('summary-plan').style.color = plan.theme.text;

        const featuresList = document.getElementById('plan-features');
        featuresList.innerHTML = (plan.features || []).map(f => `
            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4 flex-shrink-0" style="color: ${plan.theme.button};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12" stroke-width="2.5"></polyline>
                </svg>
                ${f}
            </li>
        `).join('');
    }

    function calculateTotal() {
        const days = parseInt(document.getElementById('duration_days')?.value || 30);
        const selectedPlan = document.querySelector('input[name="plan"]:checked');
        if (!selectedPlan) return;
        const plan = plans[selectedPlan.value];
        document.getElementById('summary-days').textContent = days;
        document.getElementById('summary-total').textContent = new Intl.NumberFormat('fr-FR').format(plan.price) + ' MAD';
    }
</script>
@endpush
