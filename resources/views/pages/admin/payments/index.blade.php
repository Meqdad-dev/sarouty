@extends('layouts.admin')

@section('title', 'Historique des paiements – Sarouty')
@section('page_title', 'Historique des paiements')
@section('page_subtitle', 'Suivez tous les paiements Stripe et leur statut')

@section('top_actions')
    <a href="{{ route('admin.payments.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2.5 text-sm font-medium hover:border-gold/40 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Exporter CSV
    </a>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('status') === 'completed' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-3xl font-bold text-emerald-600">{{ number_format($stats['completed']) }}</div>
            <div class="text-xs text-gray-500">Complétés</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('status') === 'pending' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-3xl font-bold text-amber-600">{{ number_format($stats['pending']) }}</div>
            <div class="text-xs text-gray-500">En attente</div>
        </div>
        <div class="panel rounded-xl p-4 text-center {{ request('status') === 'failed' ? 'ring-2 ring-red-400' : '' }}">
            <div class="text-3xl font-bold text-red-600">{{ number_format($stats['failed']) }}</div>
            <div class="text-xs text-gray-500">Échoués</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['refunded']) }}</div>
            <div class="text-xs text-gray-500">Remboursés</div>
        </div>
        <div class="panel rounded-xl p-4 text-center bg-gradient-to-br from-gold/10 to-amber-50 dark:from-gold/20 dark:to-amber-900/10 border border-gold/20">
            <div class="text-3xl font-bold text-gold">{{ number_format($stats['total_revenue'], 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-500">Revenus (MAD)</div>
        </div>
    </div>

    {{-- Revenue Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="panel rounded-2xl p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Aujourd'hui</span>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['today_revenue'], 0, ',', ' ') }} MAD</div>
        </div>
        <div class="panel rounded-2xl p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Ce mois</span>
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} MAD</div>
            @if($stats['last_month_revenue'] > 0)
                @php $growth = (($stats['monthly_revenue'] - $stats['last_month_revenue']) / $stats['last_month_revenue']) * 100 @endphp
                <div class="text-xs mt-1 {{ $growth >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                    {{ $growth >= 0 ? '↑' : '↓' }} {{ abs(round($growth)) }}% vs mois dernier
                </div>
            @endif
        </div>
        <div class="panel rounded-2xl p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Cette année</span>
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['yearly_revenue'], 0, ',', ' ') }} MAD</div>
        </div>
        <div class="panel rounded-2xl p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Panier moyen</span>
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['average_payment'], 0, ',', ' ') }} MAD</div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="panel rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenus mensuels</h3>
        <div class="h-48 flex items-end gap-2">
            @foreach($chartData as $data)
                @php $maxRevenue = max(collect($chartData)->pluck('revenue')->max(), 1) @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full bg-gold/20 rounded-t-lg relative group cursor-pointer" style="height: {{ max(4, ($data['revenue'] / $maxRevenue) * 160) }}px">
                        <div class="absolute inset-0 bg-gold rounded-t-lg opacity-60 hover:opacity-100 transition"></div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                            {{ number_format($data['revenue'], 0, ',', ' ') }} MAD
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 transform -rotate-45 origin-left whitespace-nowrap">{{ $data['month'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- By Plan Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @foreach(['starter' => 'emerald', 'pro' => 'blue', 'agence' => 'purple'] as $plan => $color)
            <div class="panel rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($plan) }}</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-700">
                        {{ $stats['by_plan'][$plan] }} ventes
                    </span>
                </div>
                <div class="text-2xl font-bold text-{{ $color }}-600">{{ number_format($stats['revenue_by_plan'][$plan], 0, ',', ' ') }} MAD</div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap items-end gap-4">
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

            <div class="w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Plan</label>
                <select name="plan" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    @foreach(\App\Models\Payment::PLANS as $value => $data)
                        <option value="{{ $value }}" {{ request('plan') === $value ? 'selected' : '' }}>{{ $data['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-32">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Période</label>
                <select name="period" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Cette année</option>
                </select>
            </div>

            <div class="w-32">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
            </div>

            <div class="w-32">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="panel rounded-2xl overflow-hidden">
        @if($payments->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucun paiement</h3>
                <p class="text-sm text-gray-500">Les paiements apparaîtront ici une fois effectués.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-4 py-4">Plan</th>
                            <th class="px-4 py-4">Montant</th>
                            <th class="px-4 py-4">Statut</th>
                            <th class="px-4 py-4">Transaction</th>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                {{-- User --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gold/10 flex items-center justify-center flex-shrink-0">
                                            @if($payment->user?->avatar)
                                                <img src="{{ $payment->user->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                            @else
                                                <span class="text-gold font-bold text-sm">{{ strtoupper(substr($payment->user->name ?? '?', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $payment->user->name ?? 'Utilisateur supprimé' }}</div>
                                            <div class="text-xs text-gray-400">{{ $payment->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Plan --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                        @if($payment->plan === 'agence') bg-purple-100 text-purple-700
                                        @elseif($payment->plan === 'pro') bg-blue-100 text-blue-700
                                        @elseif($payment->plan === 'starter') bg-emerald-100 text-emerald-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $payment->plan_label }}
                                    </span>
                                </td>

                                {{-- Amount --}}
                                <td class="px-4 py-4">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $payment->formatted_amount }}</span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                        @if($payment->status === 'completed') bg-emerald-100 text-emerald-700
                                        @elseif($payment->status === 'pending') bg-amber-100 text-amber-700
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-700
                                        @else bg-purple-100 text-purple-700 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            @if($payment->status === 'completed') bg-emerald-500
                                            @elseif($payment->status === 'pending') bg-amber-500
                                            @elseif($payment->status === 'failed') bg-red-500
                                            @else bg-purple-500 @endif"></span>
                                        {{ $payment->status_label }}
                                    </span>
                                </td>

                                {{-- Transaction --}}
                                <td class="px-4 py-4">
                                    @if($payment->stripe_payment_intent)
                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ substr($payment->stripe_payment_intent, 0, 16) }}...</code>
                                    @elseif($payment->stripe_session_id)
                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ substr($payment->stripe_session_id, 0, 16) }}...</code>
                                    @else
                                        <span class="text-xs text-gray-400">Admin</span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-4">
                                    <div class="text-xs">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $payment->created_at->format('d/m/Y') }}</div>
                                        <div class="text-gray-400">{{ $payment->created_at->format('H:i') }}</div>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.payments.show', $payment) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                           title="Voir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($payment->status === 'completed')
                                            <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" onsubmit="return confirm('Rembourser ce paiement ?')">
                                                @csrf
                                                <button type="submit" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-600 transition" title="Rembourser">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
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

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
@endsection
