@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">

        {{-- Info --}}
        <div class="text-sm text-ink/50 hidden sm:block">
            Résultats <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>
            à <span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
            sur <span class="font-medium text-ink">{{ $paginator->total() }}</span>
        </div>

        {{-- Boutons --}}
        <div class="flex items-center gap-1">

            {{-- Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-sand text-ink/30 cursor-not-allowed text-sm">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-sand hover:border-gold hover:text-gold text-ink/60 transition-all text-sm font-medium">
                    ‹
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-9 h-9 flex items-center justify-center text-ink/30 text-sm">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-gold text-white text-sm font-semibold shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-sand hover:border-gold hover:text-gold text-ink/60 transition-all text-sm font-medium">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-sand hover:border-gold hover:text-gold text-ink/60 transition-all text-sm font-medium">
                    ›
                </a>
            @else
                <span class="w-9 h-9 flex items-center justify-center rounded-xl bg-sand text-ink/30 cursor-not-allowed text-sm">
                    ›
                </span>
            @endif
        </div>
    </nav>
@endif
