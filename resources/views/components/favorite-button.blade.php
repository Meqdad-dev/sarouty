{{-- Favorite button with Alpine.js AJAX --}}
@props(['listing'])

<div x-data="{
    isFavorited: {{ auth()->check() && $listing->isFavoritedBy(auth()->user()) ? 'true' : 'false' }},
    isLoading: false,
    toggle() {
        @auth
            if (this.isLoading) return;
            this.isLoading = true;
            
            fetch('{{ route('user.favorites.toggle', $listing) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.favorited !== undefined) {
                    this.isFavorited = data.favorited;
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => this.isLoading = false);
        @else
            window.location.href = '{{ route('login') }}';
        @endauth
    }
}"
class="absolute top-3 right-3">
    <button @click="toggle"
            :disabled="isLoading"
            :class="isFavorited ? 'bg-red-500 hover:bg-red-600' : 'bg-white/90 hover:bg-white hover:scale-110'"
            class="w-9 h-9 rounded-full flex items-center justify-center shadow-sm transition-all disabled:opacity-50">
        <svg class="w-5 h-5 transition-colors"
             :class="isFavorited ? 'text-white' : 'text-ink/40'"
             :fill="isFavorited ? 'currentColor' : 'none'"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
    </button>
</div>
