@php
    $user = auth()->user();
    $listingsQuery = $user->listings();
    $allListingsCount = (clone $listingsQuery)->count();
    $activeListingsCount = (clone $listingsQuery)->where('status', 'active')->count();
    $pendingListingsCount = (clone $listingsQuery)->where('status', 'pending')->count();
    $viewsCount = (clone $listingsQuery)->sum('views');
    $favoriteCount = $user->favorites()->count();
    $unreadMessages = \App\Models\Message::where('receiver_id', $user->id)->where('status', 'approved')->where('is_read', false)->count();
    $unreadNotifications = \DB::table('user_notifications')->where('user_id', $user->id)->where('read', false)->count();

    $menu = [
        [
            'label' => 'Vue d\'ensemble',
            'route' => 'user.dashboard',
            'active' => request()->routeIs('user.dashboard'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.75L12 3l9 7.75V20a1 1 0 01-1 1h-4.75a.75.75 0 01-.75-.75V15a2.5 2.5 0 00-5 0v5.25a.75.75 0 01-.75.75H4a1 1 0 01-1-1v-9.25z" />',
        ],
        [
            'label' => 'Mes annonces',
            'route' => 'user.dashboard',
            'active' => request()->routeIs('user.dashboard') || request()->routeIs('user.listings.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6.75A1.75 1.75 0 015.75 5h12.5A1.75 1.75 0 0120 6.75v10.5A1.75 1.75 0 0118.25 19H5.75A1.75 1.75 0 014 17.25V6.75z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 9h8M8 12h8M8 15h5" />',
        ],
        [
            'label' => 'Messages',
            'route' => 'user.messages.index',
            'active' => request()->routeIs('user.messages.*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2v10z" />',
            'badge' => $unreadMessages,
        ],
        [
            'label' => 'Notifications',
            'route' => 'user.notifications',
            'active' => request()->routeIs('user.notifications*'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />',
            'badge' => $unreadNotifications,
        ],
        [
            'label' => 'Mes favoris',
            'route' => 'user.favorites',
            'active' => request()->routeIs('user.favorites'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20.25l-1.05-.95C5.4 14.26 2 11.18 2 7.38 2 4.31 4.42 2 7.34 2c1.65 0 3.24.76 4.26 2.03A5.57 5.57 0 0115.86 2C18.78 2 21.2 4.31 21.2 7.38c0 3.8-3.4 6.88-8.95 11.93l-.25.24z" />',
        ],
        [
            'label' => 'Mon profil',
            'route' => 'user.profile',
            'active' => request()->routeIs('user.profile'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 8a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 19.5a7.5 7.5 0 0115 0" />',
        ],
    ];
@endphp

<div class="space-y-5 lg:sticky lg:top-24 self-start">
    <div class="rounded-[28px] border border-white/70 bg-white/95 shadow-[0_25px_60px_-30px_rgba(26,20,16,0.35)] backdrop-blur">
        <div class="border-b border-stone-100 px-6 py-6">
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="h-16 w-16 rounded-2xl object-cover ring-4 ring-gold/10">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-gold">Espace propriétaire</p>
                    <h2 class="mt-1 truncate font-display text-3xl font-semibold text-ink">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-ink/50">{{ $user->role_label }} · depuis {{ $user->created_at->translatedFormat('M Y') }}</p>
                </div>
            </div>
        </div>

        @if(!$user->isClient())
        <div class="px-4 py-4">
            <a href="{{ route('user.listings.create') }}"
               class="flex w-full items-center justify-center gap-2 rounded-2xl bg-ink px-4 py-3 text-sm font-semibold text-white transition hover:bg-ink-light">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Publier une annonce
            </a>
        </div>
        @endif

        <nav class="space-y-1 px-3 pb-3">
            @foreach($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ $item['active'] ? 'bg-gold text-white shadow-lg shadow-gold/20' : 'text-ink/70 hover:bg-sand-light hover:text-ink' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 {{ $item['active'] ? 'text-white' : 'text-gold' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['icon'] !!}
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </div>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold {{ $item['active'] ? 'bg-white text-gold' : 'bg-red-500 text-white' }}">
                            {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach

            <a href="{{ route('listings.index') }}"
               class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-ink/70 transition hover:bg-sand-light hover:text-ink">
                <svg class="h-5 w-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21c4.97 0 9-4.03 9-9s-4.03-9-9-9-9 4.03-9 9 4.03 9 9 9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h8M12 8v8" />
                </svg>
                <span>Explorer le marché</span>
            </a>
        </nav>
    </div>

    <div class="rounded-[28px] border border-stone-200/80 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-ink/45">Performance</h3>
            <span class="rounded-full bg-gold/10 px-2.5 py-1 text-[11px] font-semibold text-gold">Temps réel</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-sand-light p-4">
                <p class="text-xs text-ink/45">Annonces</p>
                <p class="mt-1 font-display text-2xl font-semibold text-ink">{{ number_format($allListingsCount) }}</p>
            </div>
            <div class="rounded-2xl bg-sand-light p-4">
                <p class="text-xs text-ink/45">Actives</p>
                <p class="mt-1 font-display text-2xl font-semibold text-ink">{{ number_format($activeListingsCount) }}</p>
            </div>
            <div class="rounded-2xl bg-sand-light p-4">
                <p class="text-xs text-ink/45">En attente</p>
                <p class="mt-1 font-display text-2xl font-semibold text-ink">{{ number_format($pendingListingsCount) }}</p>
            </div>
            <div class="rounded-2xl bg-sand-light p-4">
                <p class="text-xs text-ink/45">Vues</p>
                <p class="mt-1 font-display text-2xl font-semibold text-ink">{{ number_format($viewsCount) }}</p>
            </div>
        </div>

        <div class="mt-4 rounded-2xl border border-dashed border-gold/30 bg-gold/5 p-4">
            <p class="text-sm font-medium text-ink">{{ number_format($favoriteCount) }} annonce(s) sauvegardée(s) dans vos favoris.</p>
            <p class="mt-1 text-xs leading-5 text-ink/55">Gardez un accès rapide à vos opportunités et suivez l'évolution de vos biens depuis un seul espace.</p>
        </div>
    </div>

    <div class="rounded-[28px] border border-stone-200/80 bg-ink p-5 text-white shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Raccourcis utiles</p>
        <div class="mt-4 space-y-3 text-sm text-white/75">
            <a href="{{ route('listings.index', ['type' => 'vente']) }}" class="flex items-center justify-between rounded-2xl border border-white/10 px-4 py-3 transition hover:border-gold/40 hover:bg-white/5">
                <span>Biens à vendre</span>
                <span class="text-gold">→</span>
            </a>
            <a href="{{ route('listings.index', ['type' => 'location']) }}" class="flex items-center justify-between rounded-2xl border border-white/10 px-4 py-3 transition hover:border-gold/40 hover:bg-white/5">
                <span>Biens à louer</span>
                <span class="text-gold">→</span>
            </a>
            <a href="{{ route('user.profile') }}" class="flex items-center justify-between rounded-2xl border border-white/10 px-4 py-3 transition hover:border-gold/40 hover:bg-white/5">
                <span>Mettre à jour mon profil</span>
                <span class="text-gold">→</span>
            </a>
        </div>
    </div>
</div>
