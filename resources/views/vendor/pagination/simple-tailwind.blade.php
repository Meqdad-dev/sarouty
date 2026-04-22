@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4">
        <div class="text-sm text-ink/50 hidden sm:block">
            Page <span class="font-semibold text-ink">{{ $paginator->currentPage() }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 rounded-xl bg-sand text-ink/30 text-sm cursor-not-allowed">← Précédent</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-4 py-2 rounded-xl bg-white border border-sand hover:border-gold hover:text-gold text-ink/60 text-sm transition-all">
                    ← Précédent
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-4 py-2 rounded-xl bg-white border border-sand hover:border-gold hover:text-gold text-ink/60 text-sm transition-all">
                    Suivant →
                </a>
            @else
                <span class="px-4 py-2 rounded-xl bg-sand text-ink/30 text-sm cursor-not-allowed">Suivant →</span>
            @endif
        </div>
    </nav>
@endif
