@extends('layouts.admin')

@section('title', 'Signalement #' . $report->id . ' – Administration')
@section('page_title', 'Détails du signalement')
@section('page_subtitle', $report->reason_label)

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="xl:col-span-2 space-y-6">
            {{-- Report Info --}}
            <div class="panel rounded-2xl p-6">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" /></svg>
                        {{ $report->reason_label }}
                    </span>
                    
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold
                        @if($report->status === 'pending') bg-amber-100 text-amber-700
                        @elseif($report->status === 'resolved') bg-emerald-100 text-emerald-700
                        @else bg-gray-100 text-gray-600 @endif">
                        @if($report->status === 'pending') En attente
                        @elseif($report->status === 'resolved') Résolu
                        @else Classé sans suite @endif
                    </span>
                </div>

                <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Signalement #{{ $report->id }}
                </h2>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $report->description ?: 'Aucune description fournie.' }}
                    </p>
                </div>

                <div class="text-xs text-gray-500 space-y-2">
                    <p class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Signalé le {{ $report->created_at->format('d/m/Y à H:i') }}
                    </p>
                    @if($report->status !== 'pending')
                        <p class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Mis à jour le {{ $report->updated_at->format('d/m/Y à H:i') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Reported Listing --}}
            @if($report->listing)
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Annonce signalée
                    </h3>

                    <div class="flex gap-4">
                        {{-- Image --}}
                        <div class="w-32 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                            @if($report->listing->images->first())
                                <img src="{{ $report->listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $report->listing->title }}</h4>
                            <p class="text-sm text-gold font-semibold mt-1">{{ $report->listing->formatted_price }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $report->listing->city }} · {{ $report->listing->transaction_label }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full
                                    @if($report->listing->status === 'active') bg-emerald-100 text-emerald-700
                                    @elseif($report->listing->status === 'pending') bg-amber-100 text-amber-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $report->listing->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('admin.listings.show', $report->listing) }}"
                           class="inline-flex items-center gap-2 text-sm text-gold hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir l'annonce complète
                        </a>
                    </div>
                </div>
            @else
                <div class="panel rounded-2xl p-6 border-red-200 dark:border-red-900/50">
                    <div class="text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <p class="text-sm">Cette annonce a été supprimée</p>
                    </div>
                </div>
            @endif

            {{-- Listing Description --}}
            @if($report->listing && $report->listing->description)
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Description de l'annonce</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {!! nl2br(e($report->listing->description)) !!}
                    </p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Reporter Info --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Signalé par
                </h3>
                
                @if($report->user)
                    <div class="flex items-center gap-3">
                        <img src="{{ $report->user->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $report->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $report->user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $report->user) }}"
                       class="mt-3 block text-center text-sm text-gold hover:underline">
                        Voir le profil →
                    </a>
                @else
                    <p class="text-gray-400">Utilisateur inconnu</p>
                @endif
            </div>

            {{-- Author Info --}}
            @if($report->listing && $report->listing->user)
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Auteur de l'annonce
                    </h3>
                    
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ $report->listing->user->avatar_url }}" alt="" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $report->listing->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $report->listing->user->email }}</p>
                            @if($report->listing->user->isBanned())
                                <span class="text-xs text-red-500 font-medium">Banni</span>
                            @endif
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.users.show', $report->listing->user) }}"
                       class="block text-center text-sm text-gold hover:underline">
                        Voir le profil →
                    </a>
                </div>
            @endif

            {{-- Transférer — toujours disponible --}}
            @if($report->listing && $report->listing->user)
                <div class="panel rounded-2xl p-6 border-2 border-indigo-200 dark:border-indigo-900">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Notifier l'auteur
                    </h3>
                    <form action="{{ route('admin.reports.forward', $report) }}" method="POST"
                          onsubmit="return confirm('Transférer ce signalement à l\'auteur de l\'annonce ?')">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Transférer à l'auteur
                        </button>
                    </form>
                </div>
            @endif

            {{-- Actions (statut pending seulement) --}}
            @if($report->status === 'pending')
                <div class="panel rounded-2xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                    <div class="space-y-3">
                        {{-- Resolve --}}
                        <form action="{{ route('admin.reports.resolve', $report) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marquer comme résolu
                            </button>
                        </form>

                        {{-- Delete Listing --}}
                        @if($report->listing)
                            <form action="{{ route('admin.reports.delete-listing', $report) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette annonce définitivement ?')">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-3 rounded-xl transition-colors">
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
                                        class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-3 rounded-xl transition-colors">
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
                                    class="w-full flex items-center justify-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold py-3 rounded-xl transition-colors">
                                Classer sans suite
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Back Button --}}
            <a href="{{ route('admin.reports.index') }}"
               class="block text-center text-sm text-gray-500 hover:text-gray-700">
                ← Retour à la liste
            </a>
        </div>
    </div>
@endsection
