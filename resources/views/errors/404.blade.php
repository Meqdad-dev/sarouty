<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable – Sarouty</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { gold: '#C8963E', sand: { DEFAULT: '#F5EFE0', light: '#FBF8F2' }, ink: '#1A1410' },
                fontFamily: { display: ['"Cormorant Garamond"', 'serif'] }
            }}
        }
    </script>
</head>
<body class="min-h-screen bg-sand-light flex items-center justify-center p-6"
      style="font-family: 'Outfit', sans-serif;">
    <div class="text-center max-w-md">
        {{-- Motif décoratif --}}
        <div class="relative inline-block mb-8">
            <div class="font-display text-[120px] font-bold text-gold/10 leading-none select-none">404</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-16 h-16 text-gold/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </div>
        </div>

        <h1 class="font-display text-4xl font-bold text-ink mb-3">Page introuvable</h1>
        <p class="text-ink/50 mb-8 leading-relaxed">
            Cette annonce n'existe plus ou a été supprimée.<br>
            Mais nous avons des milliers d'autres biens à vous proposer !
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/"
               class="bg-gold hover:bg-gold-dark text-white font-semibold px-8 py-3 rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Retour à l'accueil
            </a>
            <a href="{{ route('listings.index') }}"
               class="bg-white border border-sand/80 text-ink/70 hover:text-ink font-semibold px-8 py-3 rounded-xl transition-all hover:shadow-sm flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Voir les annonces
            </a>
        </div>
    </div>
</body>
</html>
