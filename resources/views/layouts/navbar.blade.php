<nav x-data="{ open: false, scrolled: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
     :class="scrolled ? 'bg-white shadow-md' : 'bg-white/95 backdrop-blur-sm'"
     class="fixed top-0 left-0 right-0 z-40 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-14 sm:h-16 w-auto text-gold">
            </a>

            {{-- Navigation principale (desktop) --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('listings.index', ['type' => 'vente']) }}"
                   class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Vente</a>
                <a href="{{ route('listings.index', ['type' => 'location']) }}"
                   class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Location</a>
                <a href="{{ route('listings.index', ['type' => 'neuf']) }}"
                   class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Neuf</a>
                <a href="{{ route('listings.index', ['type' => 'vacances']) }}"
                   class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Vacances</a>
                <a href="{{ route('tarifs') }}"
                   class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Tarifs</a>
                <a href="{{ route('listings.index') }}"
                   class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Toutes annonces</a>
            </div>

            {{-- Actions (desktop) --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @php
                        $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->where('status', 'approved')->where('is_read', false)->count();
                        $unreadNotifications = \DB::table('user_notifications')->where('user_id', auth()->id())->where('read', false)->count();
                        $publishRoute = auth()->user()->isAdmin() ? route('admin.listings.create') : route('user.listings.create');
                    @endphp
                    
                    {{-- Messages Icon --}}
                    <a href="{{ route('user.messages.index') }}" class="relative p-2 rounded-lg hover:bg-sand transition-colors group" title="Messages">
                        <svg class="w-5 h-5 text-ink/60 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        @if($unreadMessages > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold">
                                {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                            </span>
                        @endif
                    </a>

                    {{-- Notifications Icon --}}
                    <a href="{{ route('user.notifications') }}" class="relative p-2 rounded-lg hover:bg-sand transition-colors group" title="Notifications">
                        <svg class="w-5 h-5 text-ink/60 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadNotifications > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold">
                                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                            </span>
                        @endif
                    </a>

                    {{-- Menu utilisateur connecté --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-sand transition-colors">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                 class="w-7 h-7 rounded-full object-cover">
                            <span class="text-sm font-medium text-ink">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-ink/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-sand/80 py-1 z-50">

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-ink hover:bg-sand transition-colors">
                                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10"/>
                                    </svg>
                                    Administration
                                </a>
                                <hr class="my-1 border-sand">
                            @endif

                            <a href="{{ route('user.dashboard') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-ink hover:bg-sand transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Mon tableau de bord
                            </a>
                            <a href="{{ route('user.messages.index') }}"
                               class="flex items-center justify-between px-4 py-2 text-sm text-ink hover:bg-sand transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Messages
                                </span>
                                @if($unreadMessages > 0)
                                    <span class="flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold">{{ $unreadMessages }}</span>
                                @endif
                            </a>
                            <a href="{{ route('user.notifications') }}"
                               class="flex items-center justify-between px-4 py-2 text-sm text-ink hover:bg-sand transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    Notifications
                                </span>
                                @if($unreadNotifications > 0)
                                    <span class="flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold">{{ $unreadNotifications }}</span>
                                @endif
                            </a>
                            <a href="{{ route('user.favorites') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-ink hover:bg-sand transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Mes favoris
                            </a>
                            <a href="{{ route('user.profile') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-ink hover:bg-sand transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Mon profil
                            </a>
                            <hr class="my-1 border-sand">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(!auth()->user()->isClient())
                    <a href="{{ $publishRoute }}"
                       class="bg-gold hover:bg-gold-dark text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Publier
                    </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-ink/70 hover:text-gold transition-colors">Connexion</a>
                    <a href="{{ route('register') }}"
                       class="bg-gold hover:bg-gold-dark text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                        Inscription
                    </a>
                @endauth
            </div>

            {{-- Bouton hamburger (mobile) --}}
            <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-sand transition-colors">
                <svg x-show="!open" class="w-6 h-6 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Menu mobile --}}
        <div x-show="open" x-transition class="md:hidden border-t border-sand py-4 space-y-2">
            <a href="{{ route('listings.index', ['type' => 'vente']) }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Vente</a>
            <a href="{{ route('listings.index', ['type' => 'location']) }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Location</a>
            <a href="{{ route('listings.index', ['type' => 'neuf']) }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Neuf</a>
            <a href="{{ route('listings.index', ['type' => 'vacances']) }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Vacances</a>
            <a href="{{ route('tarifs') }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Tarifs</a>
            <hr class="border-sand">
            @auth
                @php
                    $unreadMessagesMobile = \App\Models\Message::where('receiver_id', auth()->id())->where('status', 'approved')->where('is_read', false)->count();
                    $unreadNotificationsMobile = \DB::table('user_notifications')->where('user_id', auth()->id())->where('read', false)->count();
                    $publishRouteMobile = auth()->user()->isAdmin() ? route('admin.listings.create') : route('user.listings.create');
                @endphp
                <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Mon tableau de bord</a>
                <a href="{{ route('user.messages.index') }}" class="flex items-center justify-between px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">
                    <span>Messages</span>
                    @if($unreadMessagesMobile > 0)
                        <span class="flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold">{{ $unreadMessagesMobile }}</span>
                    @endif
                </a>
                <a href="{{ route('user.notifications') }}" class="flex items-center justify-between px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">
                    <span>Notifications</span>
                    @if($unreadNotificationsMobile > 0)
                        <span class="flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold">{{ $unreadNotificationsMobile }}</span>
                    @endif
                </a>
                <a href="{{ route('user.favorites') }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Mes favoris</a>
                @if(!auth()->user()->isClient())
                <a href="{{ $publishRouteMobile }}" class="block mx-4 py-2 text-sm text-center bg-gold text-white rounded-lg">Publier une annonce</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-ink hover:bg-sand rounded-lg">Connexion</a>
                <a href="{{ route('register') }}" class="block mx-4 py-2 text-sm text-center bg-gold text-white rounded-lg">Inscription</a>
            @endauth
        </div>
    </div>
</nav>
