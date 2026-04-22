<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé – Sarouty</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
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
        <div class="relative inline-block mb-8">
            <div class="font-display text-[120px] font-bold text-red-500/10 leading-none select-none">403</div>
            <div class="absolute inset-0 flex items-center justify-center text-red-500/40">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
        </div>

        <h1 class="font-display text-4xl font-bold text-ink mb-3">Accès non autorisé</h1>
        <p class="text-ink/50 mb-8">
            Vous n'avez pas les permissions nécessaires pour accéder à cette page.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/"
               class="bg-gold hover:bg-gold-dark text-white font-semibold px-8 py-3 rounded-xl transition-all hover:shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Retour à l'accueil
            </a>
            <a href="{{ route('login') }}"
               class="bg-white border border-sand/80 text-ink/70 hover:text-ink font-semibold px-8 py-3 rounded-xl transition-all hover:shadow-sm flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                Se connecter
            </a>
        </div>
    </div>
</body>
</html>
