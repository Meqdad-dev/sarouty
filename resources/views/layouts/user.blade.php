<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sarouty</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gold: { DEFAULT: '#C8963E', light: '#E8B86D', dark: '#9B6E22' },
                    },
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'serif'],
                        body: ['Outfit', 'sans-serif'],
                    },
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --bg: #f6f7fb;
            --panel: #ffffff;
            --panel-soft: #f8fafc;
            --sidebar: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --text-soft: #6b7280;
            --nav: #4b5563;
            --nav-hover-bg: #f3f4f6;
            --nav-active-bg: rgba(200,150,62,0.14);
            --nav-active-text: #9B6E22;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        html.dark {
            --bg: #0f1117;
            --panel: #161a23;
            --panel-soft: #1d2330;
            --sidebar: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #f8fafc;
            --text-soft: rgba(248,250,252,0.6);
            --nav: rgba(248,250,252,0.65);
            --nav-hover-bg: rgba(255,255,255,0.05);
            --nav-active-bg: rgba(200,150,62,0.18);
            --nav-active-text: #E8B86D;
            --shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .font-display { font-family: 'Cormorant Garamond', serif; }
        .admin-shell { min-height: 100vh; display: flex; }
        .admin-sidebar {
            width: 280px;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        .admin-main { flex: 1; min-width: 0; }
        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: color-mix(in srgb, var(--bg) 92%, transparent);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .panel {
            background: var(--panel);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        .panel-soft {
            background: var(--panel-soft);
            border: 1px solid var(--border);
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            color: var(--nav);
            text-decoration: none;
            transition: .2s ease;
            font-size: .92rem;
            font-weight: 500;
        }
        .nav-item:hover {
            background: var(--nav-hover-bg);
            color: var(--text);
        }
        .nav-item.active {
            background: var(--nav-active-bg);
            color: var(--nav-active-text);
        }
        .nav-item.active .nav-badge {
            display: none !important;
        }
        .nav-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--panel-soft) 80%, transparent);
            border: 1px solid var(--border);
            flex-shrink: 0;
        }
        
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: rgba(148,163,184,.45); border-radius: 999px; }

        @media (max-width: 1024px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                height: 100vh;
                z-index: 40;
                transform: translateX(-100%);
                transition: transform .25s ease;
            }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15,23,42,.45);
                z-index: 30;
            }
            .admin-main {
                width: 100%;
                max-width: 100vw;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[var(--bg)] text-[var(--text)]" x-data="userLayout()" x-init="init()">
<div class="admin-shell flex h-screen w-full overflow-hidden">
    <template x-if="mobileSidebarOpen">
        <div class="admin-overlay lg:hidden fixed inset-0 bg-slate-900/40 z-30" @click="mobileSidebarOpen = false"></div>
    </template>

    <aside class="admin-sidebar flex-shrink-0 flex flex-col h-screen lg:sticky top-0 overflow-y-auto"
           :class="{ 'open': mobileSidebarOpen }">
        <div class="px-6 py-6 border-b" style="border-color: var(--border)">
            <div class="flex items-center justify-center">
                <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-14 w-auto object-contain">
            </div>
        </div>

        <div class="px-4 py-4 border-b" style="border-color: var(--border)">
            <div class="panel-soft rounded-2xl p-3 flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'U') }}"
                     alt="Avatar"
                     class="w-10 h-10 rounded-xl object-cover">
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs capitalize" style="color: var(--text-soft)">{{ auth()->user()->role ?? 'Particulier' }}</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-5 space-y-1.5">
            @if(!auth()->user()->isClient())
            <div class="text-[11px] uppercase tracking-[0.22em] px-2 pb-2" style="color: var(--text-soft)">Vue Générale</div>

            <a href="{{ route('user.dashboard') }}" class="nav-item nav-ajax {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                </span>
                <span>Tableau de bord</span>
            </a>
            @endif
            
            @if(!auth()->user()->isClient())
            <a href="{{ route('user.listings.create') }}" class="nav-item nav-ajax {{ request()->routeIs('user.listings.create') ? 'active' : '' }}">
                <span class="nav-icon text-gold">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </span>
                <span>Publier une annonce</span>
            </a>
            @endif

            <div class="text-[11px] uppercase tracking-[0.22em] px-2 pt-5 pb-2" style="color: var(--text-soft)">Mon Activité</div>

            <a href="{{ route('user.messages.index') }}" class="nav-item nav-ajax {{ request()->routeIs('user.messages.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path>
                    </svg>
                </span>
                <span>Messages</span>
                @php
                    $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
                @endphp
                @if($unreadMessages > 0)
                <span class="nav-badge ml-auto text-xs bg-gold/20 text-gold-dark font-semibold rounded-full px-2 py-0.5">{{ $unreadMessages }}</span>
                @endif
            </a>

            <a href="{{ route('user.favorites') }}" class="nav-item nav-ajax {{ request()->routeIs('user.favorites') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </span>
                <span>Favoris</span>
            </a>

            @if(!auth()->user()->isClient())
            <a href="{{ route('user.notifications') }}" class="nav-item nav-ajax {{ request()->routeIs('user.notifications') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 01-3.46 0"></path>
                    </svg>
                </span>
                <span>Notifications</span>
                @php
                    $unreadNotifications = \DB::table('user_notifications')
                        ->where('user_id', auth()->id())
                        ->where('read', false)
                        ->count();
                @endphp
                @if($unreadNotifications > 0)
                <span class="nav-badge ml-auto text-xs bg-gold/20 text-gold-dark font-semibold rounded-full px-2 py-0.5">{{ $unreadNotifications }}</span>
                @endif
            </a>
            @endif

            <div class="text-[11px] uppercase tracking-[0.22em] px-2 pt-5 pb-2" style="color: var(--text-soft)">Paramètres</div>

            <a href="{{ route('user.profile') }}" class="nav-item nav-ajax {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span>Mon profil</span>
            </a>
            
            @if(!auth()->user()->isClient())
            <a href="{{ route('user.subscription') }}" class="nav-item nav-ajax {{ request()->routeIs('user.subscription') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"></path>
                    </svg>
                </span>
                <span>Mon Plan (Quotas)</span>
            </a>
            @endif

            <div class="pt-4 mt-2 border-t" style="border-color: var(--border)">
                <a href="{{ route('home') }}" class="nav-item text-gray-700 dark:text-gray-300 hover:text-gold dark:hover:text-gold bg-gray-50/50 dark:bg-gray-800/30 border border-gray-100 dark:border-gray-700 hover:border-gold/30">
                    <span class="nav-icon !bg-transparent !border-none">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </span>
                    <span class="font-medium">Retour à l'accueil</span>
                </a>
            </div>
        </nav>

        <div class="px-4 py-4 border-t" style="border-color: var(--border)">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full text-left text-red-500 hover:text-red-600">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
                            <polyline points="16,17 21,12 16,7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </span>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-main flex-1 flex flex-col h-screen overflow-y-auto min-w-0 w-full">
        <header class="admin-topbar sticky top-0 z-20 bg-[var(--bg)]/90 backdrop-blur-md border-b" style="border-color: var(--border)">
            <div class="px-4 lg:px-8 py-4 flex items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <button type="button" class="lg:hidden flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl panel"
                            @click="mobileSidebarOpen = true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="font-display text-2xl sm:text-3xl font-bold truncate">@yield('page_title', 'Espace Personnel')</h1>
                        <p class="text-xs sm:text-sm truncate" style="color: var(--text-soft)">@yield('page_subtitle', 'Gérez vos annonces et messages')</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    @yield('top_actions')
                    <button type="button"
                            @click="toggleTheme()"
                            class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
                        <template x-if="theme === 'light'">
                            <span class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                                </svg>
                            </span>
                        </template>
                        <template x-if="theme === 'dark'">
                            <span class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                </svg>
                            </span>
                        </template>
                    </button>
                    @if(!auth()->user()->isClient())
                        <a href="{{ route('user.listings.create') }}" class="hidden sm:inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Publier
                        </a>
                        @if(auth()->user()->canCreateSponsoredListing())
                            <a href="{{ route('user.listings.create', ['sponsored' => 1]) }}" class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:border-amber-400 hover:bg-amber-100">
                                <span>Sponsored</span>
                                <span class="inline-flex items-center rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">Eligible</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </header>

        <main id="admin-main-content" class="flex-1 px-5 lg:px-8 py-6 space-y-6">
            @if(session('success'))
                <div class="panel rounded-2xl px-4 py-3 border-emerald-200 text-emerald-700 bg-emerald-50 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="panel rounded-2xl px-4 py-3 border-red-200 text-red-700 bg-red-50 mb-6">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    function userLayout() {
        return {
            theme: 'light',
            mobileSidebarOpen: false,
            init() {
                const savedTheme = localStorage.getItem('user-theme');
                this.theme = savedTheme || 'light';
                document.documentElement.classList.toggle('dark', this.theme === 'dark');

                // Masquer le badge pour le lien actif
                setTimeout(() => {
                    document.querySelectorAll('.nav-ajax.active .nav-badge').forEach(b => b.style.display = 'none');
                }, 100);
            },
            toggleTheme() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('user-theme', this.theme);
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
            }
        }
    }
</script>
@stack('scripts')
</body>
</html>