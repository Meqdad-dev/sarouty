@extends('layouts.app')
@section('title', 'Contacter le propriétaire – Sarouty')

@section('content')
<div class="pt-20 min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <a href="{{ URL::previous() }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
            <h1 class="font-display text-2xl font-bold text-white">Contacter le propriétaire</h1>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Listing Preview --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    @if($listing->images->isNotEmpty())
                        <img src="{{ $listing->images->first()->url }}" alt="{{ $listing->title }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $listing->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $listing->city }} · {{ $listing->zone }}</p>
                        <p class="text-lg font-bold text-gold mt-2">{{ $listing->formatted_price }}</p>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                            @if($listing->user->avatar)
                                <img src="{{ $listing->user->avatar_url }}" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-gold">{{ strtoupper($listing->user->name[0]) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $listing->user->name }}</p>
                                <p class="text-xs text-gray-500">Propriétaire</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Envoyer un message</h2>
                    
                    <form action="{{ route('user.messages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">

                        {{-- User Info --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Votre nom</label>
                                <input type="text" value="{{ auth()->user()->name }}" disabled
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Votre email</label>
                                <input type="email" value="{{ auth()->user()->email }}" disabled
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-600">
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Téléphone (optionnel)
                            </label>
                            <input type="tel" name="sender_phone" value="{{ old('sender_phone', auth()->user()->phone) }}"
                                   placeholder="Pour être contacté plus rapidement"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 transition">
                            @error('sender_phone')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea name="message" rows="6" required
                                      placeholder="Bonjour, je suis intéressé(e) par votre annonce..."
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 transition resize-none">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-400 mt-1">Minimum 10 caractères</p>
                        </div>

                        {{-- Submit --}}
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs text-gray-400">
                                Vos informations seront partagées avec le propriétaire
                            </p>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all shadow-lg shadow-gold/30 hover:shadow-gold/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tips --}}
                <div class="mt-6 bg-amber-50 rounded-2xl p-5 border border-amber-100">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-amber-800">Conseils pour votre message</h4>
                            <ul class="text-sm text-amber-700 mt-2 space-y-1">
                                <li>• Présentez-vous brièvement</li>
                                <li>• Précisez vos disponibilités pour une visite</li>
                                <li>• Posez vos questions sur le bien</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
