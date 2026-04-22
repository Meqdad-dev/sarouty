@extends('layouts.admin')

@section('title', 'Gestion des Messages – Sarouty')
@section('page_title', 'Gestion des Messages')
@section('page_subtitle', 'Consultez et répondez aux messages envoyés via le formulaire de contact')

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-amber-600">{{ number_format($stats['pending']) }}</div>
            <div class="text-xs text-gray-500">En attente</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-emerald-600">{{ number_format($stats['unread']) }}</div>
            <div class="text-xs text-gray-500">Non lus (approuvés)</div>
        </div>
        <div class="panel rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-gold">{{ number_format(\App\Models\Message::where('created_at', '>=', now()->subDays(7))->count()) }}</div>
            <div class="text-xs text-gray-500">Cette semaine</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="panel rounded-2xl p-5 mb-6">
        <form method="GET" action="{{ route('admin.messages.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Rechercher</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Message, email, annonce..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold">
                </div>
            </div>

            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Statut</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50">
                    <option value="">Tous</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvés</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejetés</option>
                    <option value="unread" {{ $status === 'unread' ? 'selected' : '' }}>Non lus (approuvés)</option>
                    <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Lus</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 bg-gold text-white text-sm font-semibold rounded-xl hover:bg-gold-dark transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.messages.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    {{-- Messages List --}}
    <div class="panel rounded-2xl overflow-hidden">
        @if($messages->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Aucun message</h3>
                <p class="text-sm text-gray-500">Aucun message ne correspond à vos critères.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($messages as $message)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors {{ $message->status === 'pending' ? 'bg-amber-50/50 dark:bg-amber-900/10' : '' }}"
                         x-data="{ showReply: false, showEdit: false }">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                            {{-- Status Indicator --}}
                            <div class="flex-shrink-0 hidden lg:block">
                                <div class="w-3 h-3 rounded-full {{ $message->is_read ? 'bg-gray-300' : 'bg-amber-500 animate-pulse' }}"></div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    {{-- Sender --}}
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $message->sender->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($message->sender_name ?? 'U') . '&background=C8963E&color=fff&size=32' }}" 
                                             alt="" class="w-8 h-8 rounded-full">
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $message->sender_name ?? $message->sender->name ?? 'Anonyme' }}</span>
                                            @if($message->status === 'pending')
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                    À modérer
                                                </span>
                                            @elseif($message->status === 'approved')
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                    Approuvé
                                                </span>
                                            @elseif($message->status === 'rejected')
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    Rejeté
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Contact Info --}}
                                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mb-3">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $message->sender_email ?? $message->sender->email ?? '—' }}
                                    </span>
                                    @if($message->sender_phone)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $message->sender_phone }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Listing Info --}}
                                @if($message->listing)
                                    <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                            @if($message->listing->images->first())
                                                <img src="{{ $message->listing->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                Destinataire : <span class="font-bold">{{ $message->receiver->name ?? $message->listing->user->name ?? 'Vendeur inconnu' }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">Annonce : {{ $message->listing->title }} ({{ $message->listing->city }} · {{ $message->listing->formatted_price }})</p>
                                        </div>
                                        <a href="{{ route('admin.listings.show', $message->listing) }}" 
                                           class="text-xs text-gold hover:underline">Voir</a>
                                    </div>
                                @endif

                                {{-- Message Content --}}
                                <div class="bg-white dark:bg-gray-900 rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed border border-gray-100 dark:border-gray-800">
                                    <div x-show="!showEdit">
                                        {!! nl2br(e($message->message)) !!}
                                    </div>
                                    <div x-show="showEdit" x-collapse>
                                        <form action="{{ route('admin.messages.approve', $message) }}" method="POST">
                                            @csrf
                                            <textarea name="edited_message" rows="4" required
                                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 resize-none"
                                                      placeholder="Éditer le message...">{!! $message->message !!}</textarea>
                                            <div class="flex justify-end gap-2 mt-3">
                                                <button type="button" @click="showEdit = false"
                                                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                                                    Annuler
                                                </button>
                                                <button type="submit"
                                                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                                                    Sauvegarder et Approuver
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Reply Form --}}
                                <div x-show="showReply" x-collapse class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <form action="{{ route('admin.messages.reply', $message) }}" method="POST">
                                        @csrf
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Répondre à {{ $message->sender_email ?? $message->sender->email ?? 'l\'expéditeur' }}
                                        </label>
                                        <textarea name="reply" rows="4" required
                                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-gold/50 focus:border-gold resize-none"
                                                  placeholder="Écrivez votre réponse en tant qu'admin..."></textarea>
                                        <div class="flex justify-end gap-2 mt-3">
                                            <button type="button" @click="showReply = false"
                                                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                                                Annuler
                                            </button>
                                            <button type="submit"
                                                    class="px-6 py-2 bg-gold hover:bg-gold-dark text-white text-sm font-semibold rounded-xl transition">
                                                Envoyer la réponse
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap gap-2 lg:flex-col">
                                @if($message->status === 'pending')
                                    <form action="{{ route('admin.messages.approve', $message) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-emerald-100 hover:bg-emerald-200 text-emerald-700 transition">
                                            Approuver
                                        </button>
                                    </form>
                                    
                                    <button type="button" @click="showEdit = !showEdit" class="w-full inline-flex justify-center items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 transition">
                                        Éditer
                                    </button>

                                    <form action="{{ route('admin.messages.reject', $message) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-red-50 hover:bg-red-100 text-red-600 transition">
                                            Rejeter
                                        </button>
                                    </form>
                                @endif
                                @unless($message->is_read)
                                    <form action="{{ route('admin.messages.read', $message) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 hover:bg-gray-50 text-gray-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Marquer lu
                                        </button>
                                    </form>
                                @endunless

                                <button type="button" @click="showReply = !showReply"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-gold hover:bg-gold-dark text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Répondre
                                </button>

                                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce message ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-red-50 hover:bg-red-100 text-red-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
@endpush
