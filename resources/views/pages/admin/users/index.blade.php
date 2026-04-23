@extends('layouts.admin')
@php cache()->put('admin_viewed_users_' . auth()->id(), now()); @endphp

@section('title', 'Gestion des Utilisateurs – Sarouty')
@section('page_title', 'Gestion des Utilisateurs')
@section('page_subtitle', 'Gérez les comptes utilisateurs, rôles et permissions')

@section('top_actions')
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold text-white px-4 py-2.5 text-sm font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvel utilisateur
    </a>
@endsection

@section('content')
    {{-- Filters --}}
    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Rechercher</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                </div>
            </div>

            {{-- Role Filter --}}
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Rôle</label>
                <select name="role" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous les rôles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                    <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agent immobilier</option>
                    <option value="particulier" {{ request('role') === 'particulier' ? 'selected' : '' }}>Particulier</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="w-44">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Statut</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banni</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Stats Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php
            $statusCounts = [
                'total' => \App\Models\User::count(),
                'active' => \App\Models\User::where('is_active', true)->whereNull('banned_at')->count(),
                'inactive' => \App\Models\User::where('is_active', false)->whereNull('banned_at')->count(),
                'banned' => \App\Models\User::whereNotNull('banned_at')->count(),
            ];
        @endphp
        <div class="panel rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($statusCounts['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'active' ? 'ring-2 ring-emerald-400' : '' }}">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($statusCounts['active']) }}</div>
            <div class="text-xs text-gray-500">Actifs</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'inactive' ? 'ring-2 ring-gray-400' : '' }}">
            <div class="text-2xl font-bold text-gray-600">{{ number_format($statusCounts['inactive']) }}</div>
            <div class="text-xs text-gray-500">Inactifs</div>
        </div>
        <div class="panel rounded-xl p-3 text-center {{ request('status') === 'banned' ? 'ring-2 ring-red-400' : '' }}">
            <div class="text-2xl font-bold text-red-600">{{ number_format($statusCounts['banned']) }}</div>
            <div class="text-xs text-gray-500">Bannis</div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="panel rounded-2xl overflow-hidden">
        @if($users->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucun utilisateur trouvé</h3>
                <p class="text-sm text-gray-500">Essayez de modifier vos filtres ou créez un nouvel utilisateur.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-4 py-4">Rôle</th>
                            <th class="px-4 py-4">Annonces</th>
                            <th class="px-4 py-4">Statut</th>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                {{-- User Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="" class="w-10 h-10 rounded-full">
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        @if($user->role === 'admin') bg-purple-100 text-purple-700
                                        @elseif($user->role === 'agent') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $user->role_label }}
                                    </span>
                                </td>

                                {{-- Listings Count --}}
                                <td class="px-4 py-4">
                                    <span class="text-gray-900 dark:text-white font-medium">{{ $user->listings_count }}</span>
                                    <span class="text-gray-400 text-xs">annonces</span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4">
                                    @if($user->isBanned())
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Banni
                                        </span>
                                    @elseif($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Inactif
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-4 text-gray-500 text-xs">
                                    <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- View --}}
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                           title="Voir le profil">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-gold transition"
                                           title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        @if($user->id !== auth()->id())
                                            @if($user->isBanned())
                                                {{-- Unban --}}
                                                <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                            class="p-2 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-gray-400 hover:text-emerald-600 transition"
                                                            title="Réactiver">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Ban --}}
                                                <div x-data="{ open: false }" class="relative">
                                                    <button @click="open = !open"
                                                            class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-600 transition"
                                                            title="Bannir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                    </button>
                                                    <div x-show="open" @click.away="open = false" x-transition
                                                         class="absolute z-20 top-full right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-3">
                                                        <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                                            @csrf
                                                            <textarea name="ban_reason" rows="2"
                                                                      placeholder="Raison du bannissement (optionnel)"
                                                                      class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm border border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-red-300 resize-none mb-2"></textarea>
                                                            <button type="submit"
                                                                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded-lg transition">
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
                                                        class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-gray-400 hover:text-red-600 transition"
                                                        title="Supprimer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
