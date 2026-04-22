@extends('layouts.admin')

@section('title', $user->name . ' – Administration')
@section('page_title', 'Profil Utilisateur')
@section('page_subtitle', $user->name . ' (' . $user->email . ')')

@section('top_actions')
    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Modifier
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Sidebar - User Info --}}
        <div class="space-y-6">
            {{-- Profile Card --}}
            <div class="panel rounded-2xl p-6 text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border-4 border-gold/20">
                <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <span class="inline-flex text-xs font-semibold px-3 py-1 rounded-full mt-2
                    @if($user->role === 'admin') bg-purple-100 text-purple-700
                    @elseif($user->role === 'agent') bg-blue-100 text-blue-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ $user->role_label }}
                </span>
                
                {{-- Status Badge --}}
                <div class="mt-4">
                    @if($user->isBanned())
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Compte banni
                        </span>
                    @elseif($user->is_active)
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Compte actif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Compte inactif
                        </span>
                    @endif
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Informations de contact</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">{{ $user->email }}</span>
                    </div>
                    @if($user->phone)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">{{ $user->phone }}</span>
                        </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">Membre depuis {{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Statistiques</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 text-center">
                        <div class="font-bold text-lg text-blue-700 dark:text-blue-400">{{ $user->listings->count() }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 opacity-75">Annonces</div>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 text-center">
                        <div class="font-bold text-lg text-emerald-700 dark:text-emerald-400">{{ $user->listings->where('status', 'active')->count() }}</div>
                        <div class="text-xs text-emerald-600 dark:text-emerald-400 opacity-75">Actives</div>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 text-center">
                        <div class="font-bold text-lg text-amber-700 dark:text-amber-400">{{ $user->listings->where('status', 'pending')->count() }}</div>
                        <div class="text-xs text-amber-600 dark:text-amber-400 opacity-75">En attente</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 text-center">
                        <div class="font-bold text-lg text-red-700 dark:text-red-400">{{ $user->listings->where('status', 'rejected')->count() }}</div>
                        <div class="text-xs text-red-600 dark:text-red-400 opacity-75">Refusées</div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="panel rounded-2xl p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    @if($user->id !== auth()->id())
                        {{-- Toggle Active --}}
                        <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 {{ $user->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} text-sm font-semibold py-3 rounded-xl transition-colors">
                                @if($user->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Désactiver le compte
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Activer le compte
                                @endif
                            </button>
                        </form>

                        {{-- Ban/Unban --}}
                        @if($user->isBanned())
                            <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                    Réactiver (lever le bannissement)
                                </button>
                            </form>
                        @else
                            <div x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-semibold py-3 rounded-xl transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Bannir l'utilisateur
                                </button>
                                <div x-show="open" x-transition class="mt-3">
                                    <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                        @csrf
                                        <textarea name="ban_reason" rows="3" placeholder="Raison du bannissement (optionnel)"
                                                  class="w-full px-4 py-3 bg-red-50 rounded-xl text-sm text-gray-700 border border-red-200 focus:ring-2 focus:ring-red-300 resize-none mb-2"></textarea>
                                        <button type="submit"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                                            Confirmer le bannissement
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- Delete --}}
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                              onsubmit="return confirm('Supprimer définitivement cet utilisateur et toutes ses données ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-3 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer le compte
                            </button>
                        </form>
                    @else
                        <div class="bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-sm p-4 rounded-xl text-center">
                            <svg class="w-5 h-5 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Vous ne pouvez pas modifier votre propre compte depuis cette page.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ban Info --}}
            @if($user->isBanned())
                <div class="panel rounded-2xl p-6 border-red-200 dark:border-red-900/50">
                    <h3 class="font-semibold text-red-700 dark:text-red-400 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Informations de bannissement
                    </h3>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p><span class="font-medium">Date:</span> {{ $user->banned_at->format('d/m/Y H:i') }}</p>
                        @if($user->ban_reason)
                            <p><span class="font-medium">Raison:</span> {{ $user->ban_reason }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Main Content - Listings --}}
        <div class="xl:col-span-2">
            <div class="panel rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Annonces de l'utilisateur</h3>
                    <span class="text-sm text-gray-500">{{ $user->listings->count() }} annonce(s)</span>
                </div>

                @if($user->listings->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucune annonce</h3>
                        <p class="text-sm text-gray-500">Cet utilisateur n'a pas encore publié d'annonce.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($user->listings as $listing)
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                {{-- Image --}}
                                <div class="w-20 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                    @if($listing->images->first())
                                        <img src="{{ $listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $listing->title }}</p>
                                        @if($listing->featured)
                                            <span class="text-gold text-xs">⭐</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-400">
                                        <span>{{ $listing->city }}</span>
                                        <span>•</span>
                                        <span>{{ $listing->formatted_price }}</span>
                                        <span>•</span>
                                        <span>{{ $listing->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                {{-- Status & Actions --}}
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                                        @if($listing->status === 'active') bg-emerald-100 text-emerald-700
                                        @elseif($listing->status === 'pending') bg-amber-100 text-amber-700
                                        @elseif($listing->status === 'rejected') bg-red-100 text-red-700
                                        @elseif($listing->status === 'sold') bg-blue-100 text-blue-700
                                        @elseif($listing->status === 'rented') bg-purple-100 text-purple-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $listing->status_label }}
                                    </span>
                                    <a href="{{ route('admin.listings.show', $listing) }}"
                                       class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-medium rounded-lg transition-colors">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
