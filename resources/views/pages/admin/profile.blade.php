@extends('layouts.admin')

@section('title', 'Mon Profil – Administration')
@section('page_title', 'Mon Profil')
@section('page_subtitle', 'Mettez à jour vos informations personnelles')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.25fr)_minmax(280px,0.75fr)] gap-6">
        
        {{-- Formulaire Principal --}}
        <div class="panel rounded-[30px] p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="font-display text-3xl font-semibold text-gray-900 dark:text-white">Compte d'administration</h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Gérez vos identifiants d'accès et les informations du compte gérant le site.</p>
                </div>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf
                @method('PUT')

                <div x-data="{ preview: '{{ $user->avatar_url ?? '' }}' }" class="flex flex-col gap-5 rounded-[28px] panel-soft p-6 sm:flex-row sm:items-center">
                    <img :src="preview || 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}'" alt="{{ $user->name }}" class="h-24 w-24 rounded-3xl object-cover ring-4 ring-gold/10">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Photo de compte</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">Importez une image pour l'interface de gestion du site.</p>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <label class="cursor-pointer rounded-2xl bg-gray-900 dark:bg-gray-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 dark:hover:bg-gray-600">
                                Importer une photo
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="preview = URL.createObjectURL($event.target.files[0])">
                            </label>
                            <span class="text-xs text-gray-500 dark:text-gray-400">JPEG, PNG ou WebP · max 2 Mo</span>
                        </div>
                        @error('avatar') <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-2xl border-0 panel-soft px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-gold/30 @error('name') ring-2 ring-red-400 @enderror" style="border: 1px solid var(--border)">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Téléphone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+212 6XX XXX XXX"
                               class="w-full rounded-2xl border-0 panel-soft px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-gold/30 @error('phone') ring-2 ring-red-400 @enderror" style="border: 1px solid var(--border)">
                        @error('phone') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-2xl border-0 panel-soft px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-gold/30 @error('email') ring-2 ring-red-400 @enderror" style="border: 1px solid var(--border)">
                    @error('email') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 pt-5 mt-6" style="border-top-color: var(--border)">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-semibold text-white transition shadow-md" style="background-color: var(--nav-active-text);">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar Config --}}
        <div class="space-y-6">
            <div class="panel rounded-[30px] p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Aperçu compte</p>
                <h3 class="mt-2 font-display text-2xl font-semibold text-gray-900 dark:text-white">Coordonnées</h3>
                <div class="mt-5 space-y-4">
                    <div class="rounded-2xl panel-soft p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nom</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                    </div>
                    <div class="rounded-2xl panel-soft p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    <div class="rounded-2xl panel-soft p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Téléphone</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->phone ?: 'Non renseigné' }}</p>
                    </div>
                </div>
            </div>

            <div class="panel rounded-[30px] p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Sécurité</p>
                <h3 class="mt-2 font-display text-2xl font-semibold text-gray-900 dark:text-white">Mot de passe</h3>
                <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">Utilisez le flux sécurisé par email prévu par l'authentification.</p>
                <a href="{{ route('password.request') }}"
                   class="mt-5 inline-flex flex-wrap items-center gap-2 rounded-2xl border px-4 py-3 text-sm font-semibold transition" style="border: 1px solid var(--border); color: var(--text)">
                    Réinitialiser mon mot de passe
                </a>
            </div>
        </div>

    </div>
@endsection
