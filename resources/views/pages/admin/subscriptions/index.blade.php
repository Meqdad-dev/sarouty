@extends('layouts.admin')
@php cache()->put('admin_viewed_subs_' . auth()->id(), now()); @endphp

@section('title', 'Abonnements – Sarouty')
@section('page_title', 'Abonnements')
@section('page_subtitle', 'Gérez les abonnements, leurs couleurs et l’attribution des plans')

@section('top_actions')
    <a href="{{ route('admin.subscriptions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvel abonnement
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('active') === 'yes' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-3xl font-bold text-emerald-600">{{ number_format($stats['active']) }}</div>
            <div class="text-xs text-gray-500">Actifs</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gold">{{ number_format($stats['revenue'], 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-500">Revenus (MAD)</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['mrr'], 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-500">MRR (MAD)</div>
        </div>
        <div class="panel rounded-xl p-4 text-center col-span-2">
            <div class="flex items-center justify-center gap-4 text-sm">
                <div class="text-center">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $stats['by_plan']['starter'] }}</div>
                    <div class="text-xs text-gray-500">Starter</div>
                </div>
                <div class="text-center">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $stats['by_plan']['pro'] }}</div>
                    <div class="text-xs text-gray-500">Pro</div>
                </div>
                <div class="text-center">
                    <div class="font-bold text-gray-900 dark:text-white">{{ $stats['by_plan']['agence'] }}</div>
                    <div class="text-xs text-gray-500">Agence</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Rechercher</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Utilisateur, email, transaction..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                </div>
            </div>

            <div class="w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Statut</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    @foreach(\App\Models\Payment::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Plan</label>
                <select name="plan" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    @foreach($availablePlans as $plan)
                        @if($plan->slug !== 'gratuit')
                            <option value="{{ $plan->slug }}" {{ request('plan') === $plan->slug ? 'selected' : '' }}>{{ $plan->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="w-32">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Actif</label>
                <select name="active" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    <option value="yes" {{ request('active') === 'yes' ? 'selected' : '' }}>Oui</option>
                    <option value="no" {{ request('active') === 'no' ? 'selected' : '' }}>Non</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <div class="panel rounded-2xl overflow-hidden">
        @if($subscriptions->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucun abonnement</h3>
                <p class="text-sm text-gray-500">Les abonnements apparaîtront ici une fois créés.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-4 py-4">Plan</th>
                            <th class="px-4 py-4">Statut</th>
                            <th class="px-4 py-4">Montant</th>
                            <th class="px-4 py-4">Expiration</th>
                            <th class="px-4 py-4">Transaction</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($subscriptions as $subscription)
                            @php $theme = $subscription->plan_theme_preset; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: {{ $theme['soft'] }};">
                                            @if($subscription->user?->avatar)
                                                <img src="{{ $subscription->user->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                            @else
                                                <span class="font-bold text-sm" style="color: {{ $theme['text'] }};">{{ strtoupper(substr($subscription->user->name ?? '?', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $subscription->user->name ?? 'Utilisateur supprimé' }}</div>
                                            <div class="text-xs text-gray-400">{{ $subscription->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border text-xs font-semibold" style="background: {{ $theme['soft'] }}; color: {{ $theme['text'] }}; border-color: {{ $theme['border'] }};">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $theme['hex'] }};"></span>
                                        {{ $subscription->plan_label }}
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                        @if($subscription->status === 'completed') bg-emerald-100 text-emerald-700
                                        @elseif($subscription->status === 'pending') bg-amber-100 text-amber-700
                                        @elseif($subscription->status === 'failed') bg-red-100 text-red-700
                                        @else bg-purple-100 text-purple-700 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            @if($subscription->status === 'completed') bg-emerald-500
                                            @elseif($subscription->status === 'pending') bg-amber-500
                                            @elseif($subscription->status === 'failed') bg-red-500
                                            @else bg-purple-500 @endif"></span>
                                        {{ $subscription->status_label }}
                                    </span>
                                    @if($subscription->is_active)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-blue-50 text-blue-600">Actif</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $subscription->formatted_amount }}</span>
                                </td>

                                <td class="px-4 py-4">
                                    @if($subscription->expires_at)
                                        <div class="text-xs">
                                            <div class="font-medium {{ $subscription->remaining_days > 7 ? 'text-gray-900 dark:text-white' : 'text-amber-600' }}">
                                                {{ $subscription->remaining_days }} jours restants
                                            </div>
                                            <div class="text-gray-400">{{ $subscription->expires_at->format('d/m/Y') }}</div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if($subscription->stripe_payment_intent)
                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ substr($subscription->stripe_payment_intent, 0, 16) }}...</code>
                                    @elseif($subscription->stripe_session_id)
                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ substr($subscription->stripe_session_id, 0, 16) }}...</code>
                                    @else
                                        <span class="text-xs text-gray-400">Admin</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                           title="Voir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.subscriptions.edit', $subscription) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 transition"
                                           style="--tw-text-opacity:1; color: {{ $theme['button'] }};"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($subscription->status === 'completed' && $subscription->is_active)
                                            <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Annuler cet abonnement ?')">
                                                @csrf
                                                <button type="submit" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-600 transition" title="Annuler">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
@endsection
