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
        .metric-card { border-radius: 20px; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: rgba(16,185,129,.12); color: #059669; }
        .badge-warning { background: rgba(245,158,11,.14); color: #d97706; }
        .badge-danger  { background: rgba(239,68,68,.12); color: #dc2626; }
        .badge-info    { background: rgba(59,130,246,.12); color: #2563eb; }

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
</head>
<body class="h-full bg-[var(--bg)] text-[var(--text)]" x-data="adminLayout()" x-init="init()">
<div class="admin-shell flex min-h-screen relative w-full overflow-hidden">
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
            <a href="{{ route('admin.profile') }}" class="panel-soft rounded-2xl p-3 flex items-center gap-3 hover:opacity-80 transition cursor-pointer nav-ajax {{ request()->routeIs('admin.profile') ? 'ring-1 ring-gold' : '' }}" data-route-pattern="admin/profil">
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Admin') }}"
                     alt="Avatar"
                     class="w-10 h-10 rounded-xl object-cover">
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Administrateur' }}</div>
                    <div class="text-xs" style="color: var(--text-soft)">Mon profil</div>
                </div>
            </a>
        </div>

        <nav class="flex-1 px-4 py-5 space-y-1.5">
            <div class="text-[11px] uppercase tracking-[0.22em] px-2 pb-2" style="color: var(--text-soft)">Pilotage</div>

            <a href="{{ route('admin.dashboard') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-route-pattern="admin/tableau-de-bord">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.listings.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.listings.*') ? 'active' : '' }}" data-route-pattern="admin/annonces">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        <polyline points="9,22 9,12 15,12 15,22"></polyline>
                    </svg>
                </span>
                <span>Annonces</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-route-pattern="admin/utilisateurs">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"></path>
                    </svg>
                </span>
                <span>Utilisateurs</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-route-pattern="admin/signalements">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </span>
                <span>Signalements</span>
            </a>

            <a href="{{ route('admin.messages.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" data-route-pattern="admin/messages">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path>
                    </svg>
                </span>
                <span>Messages</span>
            </a>

            <a href="{{ route('admin.sponsorships.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.sponsorships.*') ? 'active' : '' }}" data-route-pattern="admin/sponsorisations">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </span>
                <span>Sponsorisations</span>
            </a>

            <a href="{{ route('admin.subscriptions.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}" data-route-pattern="admin/abonnements">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </span>
                <span>Abonnements</span>
            </a>

            <a href="{{ route('admin.payments.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" data-route-pattern="admin/paiements">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </span>
                <span>Paiements</span>
            </a>

            <a href="{{ route('admin.estimations.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.estimations.*') ? 'active' : '' }}" data-route-pattern="admin/estimations">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </span>
                <span>Estimations</span>
                @php $estimCount = \App\Models\Estimation::whereNotNull('contact_email')->count(); @endphp
                @if($estimCount > 0)
                <span class="ml-auto text-xs bg-gold/20 text-gold-dark font-semibold rounded-full px-2 py-0.5">{{ $estimCount }}</span>
                @endif
            </a>

            <div class="text-[11px] uppercase tracking-[0.22em] px-2 pt-5 pb-2" style="color: var(--text-soft)">Configuration</div>

            <a href="{{ route('admin.settings.index') }}" class="nav-item nav-ajax {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" data-route-pattern="admin/parametres">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"></path>
                    </svg>
                </span>
                <span>Paramètres</span>
            </a>

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

    <div class="admin-main flex-1 flex flex-col min-h-screen min-w-0 w-full">
        <header class="admin-topbar">
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
                        <h1 class="font-display text-2xl sm:text-3xl font-bold truncate">@yield('page_title', 'Administration')</h1>
                        <p class="text-xs sm:text-sm truncate" style="color: var(--text-soft)">@yield('page_subtitle', now()->isoFormat('dddd D MMMM YYYY'))</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    @yield('top_actions')
                    
                    {{-- Chrono (Horloge en temps réel) --}}
                    <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-xl panel text-sm font-medium transition duration-300" 
                         x-data="{ time: new Date().toLocaleTimeString('fr-FR') }" 
                         x-init="setInterval(() => time = new Date().toLocaleTimeString('fr-FR'), 1000)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-gold" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span x-text="time"></span>
                    </div>

                    {{-- Notifications --}}
                    @php
                        $pendingListingsCount = \App\Models\Listing::where('status', 'pending')->count();
                        $recentPaymentsCount = \App\Models\Payment::where('status', 'completed')->where('created_at', '>=', now()->subDays(3))->count();
                        $recentSponsorshipsCount = \App\Models\Sponsorship::where('status', 'active')->where('created_at', '>=', now()->subDays(3))->count();
                        $totalNotifs = $pendingListingsCount + $recentPaymentsCount + $recentSponsorshipsCount;
                    @endphp
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open" class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl panel hover:border-gold/40 transition">
                            @if($totalNotifs > 0)
                            <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-[#161a23]"></span>
                            @endif
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 01-3.46 0"></path>
                            </svg>
                        </button>

                        <div x-show="open" 
                             x-transition.opacity.duration.200ms
                             style="display: none;"
                             class="absolute right-0 mt-2 w-72 panel rounded-2xl shadow-xl overflow-hidden z-50 p-2">
                            <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100 dark:border-gray-800">
                                Centre de notifications
                            </div>
                            
                            <div class="space-y-1 mt-2">
                                <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}" class="flex flex-col px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Nouvelles annonces</span>
                                        @if($pendingListingsCount > 0)
                                            <span class="bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingListingsCount }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 mt-0.5">Annonces à valider</span>
                                </a>
                                
                                <a href="{{ route('admin.payments.index') }}" class="flex flex-col px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Nouveaux paiements</span>
                                        @if($recentPaymentsCount > 0)
                                            <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $recentPaymentsCount }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 mt-0.5">Paiements récents (72h)</span>
                                </a>

                                <a href="{{ route('admin.sponsorships.index') }}" class="flex flex-col px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">Abonnements</span>
                                        @if($recentSponsorshipsCount > 0)
                                            <span class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $recentSponsorshipsCount }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 mt-0.5">Sponsorisations activées</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Thème Mode --}}
                    <button type="button"
                            @click="toggleTheme()"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl panel hover:border-gold/40 transition"
                            title="Changer de mode">
                        <template x-if="theme === 'light'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                            </svg>
                        </template>
                        <template x-if="theme === 'dark'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                        </template>
                    </button>
                </div>
            </div>
        </header>

        <main id="admin-main-content" class="flex-1 px-5 lg:px-8 py-6 space-y-6">
            @if(session('success'))
                <div class="panel rounded-2xl px-4 py-3 border-emerald-200 text-emerald-700 bg-emerald-50">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="panel rounded-2xl px-4 py-3 border-red-200 text-red-700 bg-red-50">
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
    function adminLayout() {
        return {
            theme: 'light',
            mobileSidebarOpen: false,
            init() {
                const savedTheme = localStorage.getItem('admin-theme');
                this.theme = savedTheme || 'light';
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
                
                // Initialize AJAX navigation
                this.initAjaxNavigation();
            },
            toggleTheme() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('admin-theme', this.theme);
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
            },
            initAjaxNavigation() {
                this.currentUrl = window.location.href;

                window.addEventListener('popstate', (e) => {
                    if (e.state && e.state.url) {
                        this.loadContent(e.state.url, false);
                    }
                });

                document.addEventListener('click', (e) => {
                    const link = e.target.closest('.nav-ajax');
                    if (!link) return;
                    if (link.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

                    const url = link.getAttribute('href');
                    if (!url || url === '#') return;

                    e.preventDefault();
                    this.navigate(url);
                });
            },
            navigate(url) {
                if (this.isLoading || url === this.currentUrl) return;
                this.loadContent(url, true);
            },
            async loadContent(url, pushState = true) {
                this.isLoading = true;
                
                // Add loading indicator
                const mainContent = document.getElementById('admin-main-content');
                if (mainContent) {
                    mainContent.style.opacity = '0.5';
                    mainContent.style.pointerEvents = 'none';
                }
                
                try {
                    // Request partial content
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-PJAX': 'true',
                            'Accept': 'text/html'
                        }
                    });
                    
                    if (response.ok) {
                        const html = await response.text();
                        
                        // Parse the response
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Update content area
                        const newContent = doc.getElementById('admin-main-content');
                        if (newContent && mainContent) {
                            mainContent.innerHTML = newContent.innerHTML;
                        }
                        
                        // Update page title
                        const newTitle = doc.querySelector('title');
                        if (newTitle) {
                            document.title = newTitle.textContent;
                        }
                        
                        // Update topbar title
                        const newPageTitle = doc.querySelector('[x-title]');
                        if (newPageTitle) {
                            const pageTitleEl = document.querySelector('[x-title]');
                            if (pageTitleEl) {
                                pageTitleEl.textContent = newPageTitle.textContent;
                            }
                        }
                        
                        // Update active nav state
                        document.querySelectorAll('.nav-ajax').forEach(link => {
                            link.classList.remove('active');
                            const href = link.getAttribute('href');
                            if (url.includes(href) || link.closest('[data-route-match]')) {
                                const routePattern = link.getAttribute('data-route-pattern');
                                if (routePattern) {
                                    if (url.includes(routePattern)) {
                                        link.classList.add('active');
                                    }
                                } else if (href && url.includes(href)) {
                                    link.classList.add('active');
                                }
                            }
                        });
                        
                        // Push to history
                        if (pushState) {
                            window.history.pushState({ url: url }, '', url);
                            this.currentUrl = url;
                        }
                        
                        // Re-initialize any scripts in the new content
                        this.reinitScripts();
                        
                    } else {
                        // Fallback to full page load on error
                        window.location.href = url;
                    }
                } catch (error) {
                    console.error('Navigation error:', error);
                    window.location.href = url;
                } finally {
                    this.isLoading = false;
                    if (mainContent) {
                        mainContent.style.opacity = '1';
                        mainContent.style.pointerEvents = 'auto';
                    }
                }
            },
            reinitScripts() {
                // Re-initialize Alpine components in the new content
                const mainContent = document.getElementById('admin-main-content');
                if (mainContent && window.Alpine) {
                    mainContent.querySelectorAll('[x-data]:not([x-initialized])').forEach(el => {
                        el.setAttribute('x-initialized', 'true');
                        Alpine.initTree(el);
                    });
                }
            }
        }
    }
</script>
@yield('scripts')
@stack('scripts')
</body>
</html>
