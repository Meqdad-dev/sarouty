@extends('layouts.user')

@section('title', 'Conversation – Mon Espace')
@section('page_title', $message->subject ?? 'Conversation')
@section('page_subtitle')
    @if($message->listing)
        Concernant: {{ $message->listing->title }}
    @else
        Conversation avec un utilisateur
    @endif
@endsection

@section('top_actions')
    <a href="{{ route('user.messages.index') }}" class="inline-flex items-center gap-2 panel rounded-xl px-4 py-2 text-sm font-medium hover:border-gold/40 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour aux messages
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Message Thread --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($thread as $msg)
                @php
                    $isOwn = $msg->sender_id === auth()->id();
                @endphp
                <div class="panel rounded-[24px] overflow-hidden {{ $msg->id === $message->id ? 'ring-2 ring-gold/50' : '' }}">
                    {{-- Message Header --}}
                    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $isOwn ? 'from-gold to-gold-dark' : 'from-gray-300 to-gray-400 dark:from-gray-600 dark:to-gray-700' }} flex items-center justify-center">
                                @if($msg->sender && $msg->sender->avatar)
                                    <img src="{{ $msg->sender->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($msg->sender_name ?? $msg->sender?->name ?? '?', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $msg->sender_name ?? $msg->sender?->name ?? 'Anonyme' }}</span>
                                @if($isOwn)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">(vous)</span>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    {{-- Message Body --}}
                    <div class="px-5 py-4">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line text-sm">{{ $msg->message }}</p>
                    </div>


                </div>
            @endforeach

            {{-- Reply Form --}}
            <div class="panel rounded-[24px] p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Répondre</h3>
                <form action="{{ route('user.messages.reply', $message) }}" method="POST">
                    @csrf
                    <textarea name="message" rows="4" required
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gold/50 focus:border-gold resize-none"
                              placeholder="Écrivez votre réponse..."></textarea>
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="inline-flex items-center gap-2 bg-gold text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gold-dark transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Envoyer la réponse
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Listing Info --}}
            @if($message->listing)
                <div class="panel rounded-[24px] p-4">
                    <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden mb-3">
                        @if($message->listing->images->first())
                            <img src="{{ $message->listing->images->first()->url }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $message->listing->title }}</h4>
                    <p class="text-lg font-bold text-gold mt-1">{{ $message->listing->formatted_price }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $message->listing->city }}</p>
                    <a href="{{ route('listings.show', $message->listing) }}" 
                       class="mt-4 block text-center py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:border-gold hover:text-gold dark:hover:border-gold dark:hover:text-gold transition">
                        Voir l'annonce
                    </a>
                </div>
            @endif

            {{-- Other Person Info --}}
            @php
                $otherUser = $message->sender_id === auth()->id() ? $message->receiver : $message->sender;
            @endphp
            @if($otherUser || $message->sender_email)
                <div class="panel rounded-[24px] p-5">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Interlocuteur</h4>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center">
                                @if($otherUser && $otherUser->avatar)
                                    <img src="{{ $otherUser->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    <span class="text-gray-600 dark:text-gray-300 font-bold text-sm">{{ strtoupper(substr($message->sender_name ?? $otherUser?->name ?? '?', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $message->sender_name ?? $otherUser?->name ?? 'Client' }}</p>
                            </div>
                        </div>

                        <div class="text-sm space-y-2 mt-2">
                            @if($message->sender_email)
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <a href="mailto:{{ $message->sender_email }}" class="text-gold hover:underline">{{ $message->sender_email }}</a>
                                </div>
                            @endif

                            @if($message->sender_phone)
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <a href="tel:{{ $message->sender_phone }}" class="text-gold hover:underline">{{ $message->sender_phone }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="panel rounded-[24px] p-5">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Actions</h4>
                <div class="space-y-2">
                    <form action="{{ route('user.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Supprimer cette conversation ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
