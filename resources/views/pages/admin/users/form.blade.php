@extends('layouts.admin')

@section('title', ($user->exists ? 'Modifier' : 'Créer') . ' un utilisateur – Sarouty')
@section('page_title', $user->exists ? 'Modifier l\'utilisateur' : 'Créer un utilisateur')
@section('page_subtitle', $user->exists ? $user->name : 'Remplissez les informations pour créer un nouveau compte')

@section('top_actions')
    @if($user->exists)
        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2.5 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Voir le profil
        </a>
    @endif
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="panel rounded-2xl p-8">
            <form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                @if($user->exists) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold @error('name') ring-2 ring-red-400 @enderror"
                               placeholder="Ex: Jean Dupont">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold @error('email') ring-2 ring-red-400 @enderror"
                               placeholder="Ex: jean@example.com">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                               placeholder="Ex: +212 6 12 34 56 78">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rôle <span class="text-red-500">*</span></label>
                        <select name="role" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                            <option value="particulier" {{ old('role', $user->role) === 'particulier' ? 'selected' : '' }}>Particulier</option>
                            <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>Agent immobilier</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrateur</option>
                        </select>
                    </div>

                    {{-- Password --}}
                    <div class="{{ $user->exists ? 'md:col-span-1' : 'md:col-span-2' }}">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Mot de passe 
                            @if(!$user->exists) <span class="text-red-500">*</span> @endif
                        </label>
                        <input type="password" name="password" {{ $user->exists ? '' : 'required' }}
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold @error('password') ring-2 ring-red-400 @enderror"
                               placeholder="{{ $user->exists ? 'Laisser vide pour ne pas modifier' : 'Minimum 8 caractères' }}">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                               placeholder="Confirmer le mot de passe">
                    </div>
                </div>

                @if($user->exists)
                    {{-- Status Info --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Statut actuel</div>
                            <div class="text-sm text-gray-500">
                                @if($user->isBanned())
                                    <span class="text-red-600">Compte banni</span>
                                @elseif($user->is_active)
                                    <span class="text-emerald-600">Compte actif</span>
                                @else
                                    <span class="text-gray-600">Compte inactif</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $user) }}" class="text-gold text-sm font-medium hover:underline">
                            Gérer le statut →
                        </a>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-3 justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-medium transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                            class="bg-gold hover:bg-gold-dark text-white font-semibold px-8 py-3 rounded-xl transition-colors shadow-lg shadow-gold/30">
                        {{ $user->exists ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
