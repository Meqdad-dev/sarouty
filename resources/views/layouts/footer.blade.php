<footer class="bg-ink text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">

            {{-- Branding --}}
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" class="h-16 sm:h-20 w-auto">
                </a>
                <p class="text-white/60 text-sm leading-relaxed mb-6">
                    La première plateforme immobilière au Maroc. Trouvez votre bien idéal parmi des milliers d'annonces vérifiées.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 bg-white/10 hover:bg-gold rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-white/10 hover:bg-gold rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.369 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-white/10 hover:bg-gold rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Recherche rapide --}}
            <div>
                <h3 class="font-semibold text-sm uppercase tracking-widest text-gold mb-5">Recherche rapide</h3>
                <ul class="space-y-2.5">
                    @foreach(['Casablanca', 'Marrakech', 'Rabat', 'Tanger', 'Agadir', 'Fès'] as $city)
                        <li>
                            <a href="{{ route('listings.index', ['city' => $city]) }}"
                               class="text-sm text-white/60 hover:text-gold transition-colors">
                                Immobilier {{ $city }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Types de biens --}}
            <div>
                <h3 class="font-semibold text-sm uppercase tracking-widest text-gold mb-5">Types de biens</h3>
                <ul class="space-y-2.5">
                    @foreach(['vente' => 'Appartements à vendre', 'location' => 'Villas à louer', 'neuf' => 'Programmes neufs', 'vacances' => 'Location vacances'] as $type => $label)
                        <li>
                            <a href="{{ route('listings.index', ['type' => $type]) }}"
                               class="text-sm text-white/60 hover:text-gold transition-colors">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                    <li><a href="{{ route('listings.index', ['property' => 'riad']) }}" class="text-sm text-white/60 hover:text-gold transition-colors">Riads</a></li>
                    <li><a href="{{ route('listings.index', ['property' => 'terrain']) }}" class="text-sm text-white/60 hover:text-gold transition-colors">Terrains</a></li>
                </ul>
            </div>

            {{-- Contact & Liens --}}
            <div>
                <h3 class="font-semibold text-sm uppercase tracking-widest text-gold mb-5">Informations</h3>
                <ul class="space-y-2.5">
                    <li><a href="#" class="text-sm text-white/60 hover:text-gold transition-colors">À propos</a></li>
                    <li><a href="#" class="text-sm text-white/60 hover:text-gold transition-colors">Comment ça marche</a></li>
                    <li><a href="#" class="text-sm text-white/60 hover:text-gold transition-colors">Nos tarifs</a></li>
                    <li><a href="#" class="text-sm text-white/60 hover:text-gold transition-colors">Conditions d'utilisation</a></li>
                    <li><a href="#" class="text-sm text-white/60 hover:text-gold transition-colors">Politique de confidentialité</a></li>
                    <li><a href="#" class="text-sm text-white/60 hover:text-gold transition-colors">Contact</a></li>
                </ul>
                <div class="mt-6 pt-6 border-t border-white/10 space-y-2">
                    <p class="text-xs text-white/40 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        +212 5XX-XXXXXX
                    </p>
                    <p class="text-xs text-white/40 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        contact@sarouty.ma
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-white/40 flex items-center gap-1.5">
                © {{ date('Y') }} Sarouty. Tous droits réservés. Fait avec 
                <svg class="w-3.5 h-3.5 text-red-500 fill-current" viewBox="0 0 20 20">
                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                </svg>
                au Maroc.
            </p>
            <div class="flex gap-4">
                <a href="#" class="text-xs text-white/40 hover:text-gold transition-colors">CGU</a>
                <a href="#" class="text-xs text-white/40 hover:text-gold transition-colors">Confidentialité</a>
                <a href="#" class="text-xs text-white/40 hover:text-gold transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>
