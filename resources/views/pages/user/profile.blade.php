@extends('layouts.user')

@section('title', 'Mon Profil – Mon Espace')
@section('page_title', 'Mon Profil')
@section('page_subtitle', 'Mettez à jour vos informations personnelles')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.25fr)_minmax(280px,0.75fr)] gap-6">
        
        {{-- Formulaire Principal --}}
        <div class="panel rounded-[30px] p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Informations personnelles</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold text-gray-900 dark:text-white">Profil public</h2>
                </div>
            </div>

            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
                @csrf
                @method('PUT')

                <div x-data="{ preview: '{{ $user->avatar_url ?? '' }}' }" class="flex flex-col gap-5 rounded-[28px] bg-gray-50 dark:bg-gray-800/50 p-6 sm:flex-row sm:items-center">
                    <img :src="preview || 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}'" alt="{{ $user->name }}" class="h-24 w-24 rounded-3xl object-cover ring-4 ring-gold/10">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Photo de profil</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">Choisissez une photo nette pour recréer un lien de confiance.</p>
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
                               class="w-full rounded-2xl border-0 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-gold/30 @error('name') ring-2 ring-red-400 @enderror">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Téléphone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+212 6XX XXX XXX"
                               class="w-full rounded-2xl border-0 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-gold/30 @error('phone') ring-2 ring-red-400 @enderror">
                        @error('phone') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-white">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full cursor-not-allowed rounded-2xl border-0 bg-gray-100 dark:bg-gray-800/80 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">L’adresse email reste verrouillée depuis cet espace.</p>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-gold px-6 py-3 text-sm font-semibold text-white transition hover:bg-gold-dark shadow-md">
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
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nom</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 p-4">
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
                   class="mt-5 inline-flex items-center gap-2 rounded-2xl border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 transition hover:border-gold hover:text-gold dark:hover:text-gold">
                    Réinitialiser mon mot de passe
                </a>
            </div>
        </div>

    </div>
@endsection
