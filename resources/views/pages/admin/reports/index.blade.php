@extends('layouts.admin')
@php cache()->put('admin_viewed_reports_' . auth()->id(), now()); @endphp

@section('title', 'Gestion des Signalements – Sarouty')
@section('page_title', 'Gestion des Signalements')
@section('page_subtitle', 'Traitez les signalements et prenez les mesures nécessaires')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php
            $statusCounts = [
                'total' => \App\Models\Report::count(),
                'pending' => \App\Models\Report::where('status', 'pending')->count(),
                'resolved' => \App\Models\Report::where('status', 'resolved')->count(),
                'reviewed' => \App\Models\Report::where('status', 'reviewed')->count(),
            ];
        @endphp
        <div class="panel rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statusCounts['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'pending' ? 'ring-2 ring-amber-400' : '' }}">
            <div class="text-2xl font-bold text-amber-600">{{ number_format($statusCounts['pending']) }}</div>
            <div class="text-xs text-gray-500">En attente</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'resolved' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($statusCounts['resolved']) }}</div>
            <div class="text-xs text-gray-500">Résolus</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'reviewed' ? 'ring-2 ring-gray-400' : '' }}">
            <div class="text-2xl font-bold text-gray-600">{{ number_format($statusCounts['reviewed']) }}</div>
            <div class="text-xs text-gray-500">Classés</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Statut</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Résolus</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Classés sans suite</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Motif</label>
                <select name="reason" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous les motifs</option>
                    @foreach($reasons as $value => $label)
                        <option value="{{ $value }}" {{ request('reason') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Reports List --}}
    <div class="panel rounded-2xl overflow-hidden">
        @if($reports->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucun signalement</h3>
                <p class="text-sm text-gray-500">Tous les signalements ont été traités.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($reports as $report)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                            {{-- Listing Image --}}
                            <div class="w-full lg:w-32 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                @if($report->listing && $report->listing->images->first())
                                    <img src="{{ $report->listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Report Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    {{-- Reason Badge --}}
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" /></svg>
                                        {{ $report->reason_label }}
                                    </span>
                                    
                                    {{-- Status Badge --}}
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                        @if($report->status === 'pending') bg-amber-100 text-amber-700
                                        @elseif($report->status === 'resolved') bg-emerald-100 text-emerald-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        @if($report->status === 'pending') En attente
                                        @elseif($report->status === 'resolved') Résolu
                                        @else Classé @endif
                                    </span>
                                </div>

                                {{-- Listing Title --}}
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                                    @if($report->listing)
                                        {{ $report->listing->title }}
                                    @else
                                        <span class="text-gray-400">Annonce supprimée</span>
                                    @endif
                                </h3>

                                {{-- Reporter Info --}}
                                <p class="text-sm text-gray-500 mb-2">
                                    Signalé par 
                                    <span class="font-medium">{{ $report->user->name ?? 'Utilisateur inconnu' }}</span>
                                    le {{ $report->created_at->format('d/m/Y à H:i') }}
                                </p>

                                {{-- Description --}}
                                @if($report->description)
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                        {{ $report->description }}
                                    </div>
                                @endif

                                {{-- Author Info --}}
                                @if($report->listing && $report->listing->user)
                                    <div class="flex items-center gap-2 text-xs text-gray-400">
                                        <span>Auteur:</span>
                                        <img src="{{ $report->listing->user->avatar_url }}" alt="" class="w-5 h-5 rounded-full">
                                        <span>{{ $report->listing->user->name }}</span>
                                        @if($report->listing->user->isBanned())
                                            <span class="text-red-500 font-medium">(banni)</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap gap-2 lg:flex-col">
                                @if($report->status === 'pending')
                                    {{-- View Details --}}
                                    <a href="{{ route('admin.reports.show', $report) }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Détails
                                    </a>

                                    {{-- Resolve --}}
                                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-emerald-100 hover:bg-emerald-200 text-emerald-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Résoudre
                                        </button>
                                    </form>

                                    {{-- Delete Listing --}}
                                    @if($report->listing)
                                        <form action="{{ route('admin.reports.delete-listing', $report) }}" method="POST"
                                              onsubmit="return confirm('Supprimer cette annonce ?')">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-red-100 hover:bg-red-200 text-red-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Supprimer l'annonce
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Ban User --}}
                                    @if($report->listing && $report->listing->user && $report->listing->user->id !== auth()->id())
                                        <form action="{{ route('admin.reports.ban-user', $report) }}" method="POST"
                                              onsubmit="return confirm('Bannir cet utilisateur ?')">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-gray-900 hover:bg-black text-white transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Bannir l'auteur
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Dismiss --}}
                                    <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 hover:bg-gray-50 text-gray-600 transition">
                                            Classer sans suite
                                        </button>
                                    </form>
                                @else
                                    {{-- View Details --}}
                                    <a href="{{ route('admin.reports.show', $report) }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                                        Voir les détails
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
@endsection
