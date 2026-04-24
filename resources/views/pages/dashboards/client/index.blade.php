@extends('layouts.user')

@section('title', 'Mes Favoris – Mon Espace')
@section('page_title', 'Mes Favoris')
@section('page_subtitle', 'Retrouvez vos annonces sauvegardées')

@section('top_actions')
    <a href="{{ route('listings.index') }}" class="topbar-btn inline-flex items-center justify-center gap-2 rounded-xl panel px-4 py-2 text-sm font-medium hover:border-gold/40 transition w-full sm:w-auto">
        Explorer le marché
    </a>
@endsection

@section('content')
    <div class="panel rounded-[30px] p-5 shadow-sm sm:p-7 mb-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Collection</p>
                <h2 class="mt-2 font-display text-3xl font-semibold text-gray-900 dark:text-white">Annonces sauvegardées ({{ number_format($favorites->total()) }})</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Conservez vos repères et comparez les biens en un clic.</p>
            </div>
        </div>
    </div>

    @if($favorites->isEmpty())
        <div class="panel rounded-[30px] px-6 py-16 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-800">
                <svg class="h-9 w-9 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20.25l-1.05-.95C5.4 14.26 2 11.18 2 7.38 2 4.31 4.42 2 7.34 2c1.65 0 3.24.76 4.26 2.03A5.57 5.57 0 0115.86 2C18.78 2 21.2 4.31 21.2 7.38c0 3.8-3.4 6.88-8.95 11.93l-.25.24z" />
                </svg>
            </div>
            <h3 class="mt-6 font-display text-2xl font-semibold text-gray-900 dark:text-white">Aucun favori pour le moment</h3>
            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">Ajoutez des biens à votre sélection pour construire une vraie short-list commerciale ou personnelle.</p>
            <a href="{{ route('listings.index') }}"
               class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-gold px-6 py-3 text-sm font-semibold text-white transition hover:bg-gold-dark">
                Découvrir des annonces
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 mb-6">
            @foreach($favorites as $listing)
                @include('components.listing-card', ['listing' => $listing])
            @endforeach
        </div>

        @if($favorites->hasPages())
            <div class="panel rounded-[24px] px-4 sm:px-6 py-5 shadow-sm">
                {{ $favorites->links() }}
            </div>
        @endif
    @endif
@endsection
