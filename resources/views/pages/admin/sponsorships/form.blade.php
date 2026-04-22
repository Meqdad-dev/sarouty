@extends('layouts.admin')

@section('title', ($sponsorship->exists ? 'Modifier' : 'Créer') . ' une sponsorisation – Sarouty')
@section('page_title', $sponsorship->exists ? 'Modifier la sponsorisation' : 'Créer une sponsorisation')
@section('page_subtitle', $sponsorship->exists ? '#' . $sponsorship->id : 'Sponsoriser une annonce')

@section('top_actions')
    @if($sponsorship->exists)
        <a href="{{ route('admin.sponsorships.show', $sponsorship) }}" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2.5 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Voir les détails
        </a>
    @endif
@endsection

@section('content')
    <form action="{{ $sponsorship->exists ? route('admin.sponsorships.update', $sponsorship) : route('admin.sponsorships.store') }}" method="POST">
        @csrf
        @if($sponsorship->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Listing Selection --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a2 2 0 01-2 2h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Annonce à sponsoriser
                    </h3>

                    @if($listing)
                        {{-- Show selected listing --}}
                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                        <input type="hidden" name="user_id" value="{{ $listing->user_id }}">
                        
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border-2 border-gold/30">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-200 flex-shrink-0">
                                @if($listing->images->first())
                                    <img src="{{ $listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a2 2 0 01-2 2h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $listing->title }}</h4>
                                <p class="text-sm text-gray-500 mb-1">{{ $listing->city }} • {{ $listing->property_label }}</p>
                                <p class="text-lg font-bold text-gold">{{ number_format($listing->price, 0, ',', ' ') }} MAD</p>
                            </div>
                            @if(!$sponsorship->exists)
                                <a href="{{ route('admin.sponsorships.create') }}" class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @else
                        {{-- Listing selector --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID de l'annonce</label>
                                <input type="number" name="listing_id" value="{{ old('listing_id') }}" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                       placeholder="Ex: 123" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID de l'utilisateur</label>
                                <input type="number" name="user_id" value="{{ old('user_id') }}" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                       placeholder="Ex: 456" required>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sponsorship Type --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        Type de sponsorisation
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($types as $typeKey => $typeData)
                            <label class="relative block p-4 rounded-xl border-2 cursor-pointer transition
                                         {{ old('type', $sponsorship->type ?? '') === $typeKey ? 'border-gold bg-gold/5' : 'border-gray-200 dark:border-gray-700 hover:border-gold/40' }}">
                                <input type="radio" name="type" value="{{ $typeKey }}" 
                                       class="sr-only" 
                                       {{ old('type', $sponsorship->type ?? '') === $typeKey ? 'checked' : '' }}
                                       onchange="updateTypeDetails('{{ $typeKey }}', {{ $typeData['days'] }}, {{ $typeData['price'] }})">
                                <div class="text-center">
                                    <div class="font-semibold text-gray-900 dark:text-white mb-1">{{ $typeData['label'] }}</div>
                                    <div class="text-2xl font-bold text-gold mb-1">{{ $typeData['price'] }} MAD</div>
                                    <div class="text-xs text-gray-500">{{ $typeData['days'] }} jours</div>
                                </div>
                                @if(old('type', $sponsorship->type ?? '') === $typeKey)
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-gold flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Duration & Pricing --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Durée et tarification</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durée (jours)</label>
                            <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $sponsorship->duration_days ?? 7) }}" min="1" max="90"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                                   onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant (MAD)</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $sponsorship->amount ?? 99) }}" min="0" step="0.01"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Planification</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de début</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $sponsorship->starts_at?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        </div>
                        @if($sponsorship->exists)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'expiration</label>
                                <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $sponsorship->expires_at?->format('Y-m-d\TH:i')) }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Admin Notes --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes administrateur</h3>
                    <textarea name="admin_notes" rows="3" 
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold"
                              placeholder="Notes internes (non visibles par le client)...">{{ old('admin_notes', $sponsorship->admin_notes) }}</textarea>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statut</h3>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold/50 focus:border-gold">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $sponsorship->status ?? 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Summary --}}
                <div class="panel rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Récapitulatif</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Type</span>
                            <span class="font-medium text-gray-900 dark:text-white" id="summary-type">{{ $types[$sponsorship->type ?? 'basic']['label'] ?? 'Basique' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Durée</span>
                            <span class="font-medium text-gray-900 dark:text-white"><span id="summary-days">{{ $sponsorship->duration_days ?? 7 }}</span> jours</span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="font-bold text-gold" id="summary-total">{{ number_format($sponsorship->amount ?? 99, 0, ',', ' ') }} MAD</span>
                        </div>
                    </div>
                </div>

                {{-- Performance (edit only) --}}
                @if($sponsorship->exists)
                    <div class="panel rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance actuelle</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Impressions</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($sponsorship->impressions) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Clics</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($sponsorship->clicks) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">CTR</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $sponsorship->ctr }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Contacts</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($sponsorship->contacts) }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="panel rounded-2xl p-6">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-gold text-white font-semibold hover:bg-gold-dark transition shadow-lg shadow-gold/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $sponsorship->exists ? 'Enregistrer les modifications' : 'Créer la sponsorisation' }}
                    </button>
                    <a href="{{ route('admin.sponsorships.index') }}" class="block text-center py-2.5 mt-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const types = @json($types);
    
    function updateTypeDetails(typeKey, days, price) {
        document.getElementById('duration_days').value = days;
        document.getElementById('amount').value = price;
        document.getElementById('summary-type').textContent = types[typeKey].label;
        document.getElementById('summary-days').textContent = days;
        document.getElementById('summary-total').textContent = new Intl.NumberFormat('fr-FR').format(price) + ' MAD';
    }
    
    function calculateTotal() {
        const days = parseInt(document.getElementById('duration_days').value) || 0;
        const selectedType = document.querySelector('input[name="type"]:checked');
        const pricePerDay = selectedType ? types[selectedType.value].price / types[selectedType.value].days : 14;
        const total = days * pricePerDay;
        
        document.getElementById('amount').value = total.toFixed(2);
        document.getElementById('summary-days').textContent = days;
        document.getElementById('summary-total').textContent = new Intl.NumberFormat('fr-FR').format(Math.round(total)) + ' MAD';
    }
</script>
@endpush
