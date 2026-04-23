@extends('layouts.user')
@php cache()->put('user_viewed_messages_' . auth()->id(), now()); @endphp

@section('title', 'Mes Messages – Mon Espace')
@section('page_title', 'Mes Messages')
@section('page_subtitle', 'Gérez vos conversations')

@section('top_actions')
    @if($stats['inbox_unread'] > 0)
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
            {{ $stats['inbox_unread'] }} non lu{{ $stats['inbox_unread'] > 1 ? 's' : '' }}
        </span>
    @endif
@endsection

@section('content')
    {{-- Tabs Navigation --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-800">
        <a href="{{ route('user.messages.index', ['tab' => 'inbox']) }}"
           class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition-colors
                  {{ $tab === 'inbox' ? 'border-gold text-gold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            Boîte de réception
            @if($stats['inbox_unread'] > 0)
                <span class="ml-1 w-5 h-5 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold">{{ $stats['inbox_unread'] }}</span>
            @endif
        </a>
        <a href="{{ route('user.messages.index', ['tab' => 'sent']) }}"
           class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition-colors
                  {{ $tab === 'sent' ? 'border-gold text-gold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Messages envoyés
            <span class="ml-1 text-xs opacity-60">({{ $stats['sent_total'] }})</span>
        </a>
    </div>

    {{-- Inbox Tab --}}
    @if($tab === 'inbox')
        <div class="panel rounded-[24px] overflow-hidden">
            @if($inbox->isEmpty())
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1 text-lg">Aucun message</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Votre boîte de réception est vide.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($inbox as $message)
                        <a href="{{ route('user.messages.show', $message) }}" 
                           class="block px-6 py-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ !$message->is_read ? 'bg-gold/5 dark:bg-gold/10' : '' }}">
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center flex-shrink-0">
                                    @if($message->sender && $message->sender->avatar)
                                        <img src="{{ $message->sender->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-gray-600 dark:text-gray-300 font-bold">{{ strtoupper(substr($message->sender_name ?? $message->sender?->name ?? '?', 0, 1)) }}</span>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="font-semibold text-gray-900 dark:text-white {{ !$message->is_read ? 'text-gold dark:text-gold-light' : '' }}">
                                            {{ $message->sender_name ?? $message->sender?->name ?? 'Anonyme' }}
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $message->time_ago }}</span>
                                    </div>
                                    
                                    @if($message->listing)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 truncate">
                                            Concernant: <span class="font-medium">{{ $message->listing->title }}</span>
                                        </p>
                                    @endif

                                    <p class="text-sm {{ !$message->is_read ? 'text-gray-800 dark:text-gray-200 font-medium' : 'text-gray-500 dark:text-gray-400' }} line-clamp-2">
                                        {{ $message->excerpt }}
                                    </p>
                                </div>

                                {{-- Unread indicator --}}
                                @if(!$message->is_read)
                                    <span class="w-3 h-3 rounded-full bg-gold flex-shrink-0 mt-3"></span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($inbox->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        {{ $inbox->appends(['tab' => 'inbox'])->links() }}
                    </div>
                @endif
            @endif
        </div>

    {{-- Sent Tab --}}
    @elseif($tab === 'sent')
        <div class="panel rounded-[24px] overflow-hidden">
            @if($sent->isEmpty())
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-2xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1 text-lg">Aucun message envoyé</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Vous n'avez pas encore envoyé de messages.</p>
                    <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-2 bg-gold text-white px-6 py-3 rounded-xl text-sm font-semibold hover:bg-gold-dark transition">
                        Parcourir les annonces
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($sent as $message)
                        <a href="{{ route('user.messages.show', $message) }}" class="block px-6 py-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-start gap-4">
                                {{-- Listing image --}}
                                @if($message->listing)
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 flex-shrink-0">
                                        @if($message->listing->images->first())
                                            <img src="{{ $message->listing->images->first()->url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a2 2 0 01-2 2h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            À: <span class="font-semibold text-gray-900 dark:text-white">{{ $message->receiver?->name ?? $message->listing?->user?->name ?? 'Propriétaire' }}</span>
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $message->time_ago }}</span>
                                    </div>
                                    
                                    @if($message->listing)
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $message->listing->title }}</p>
                                    @endif

                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1">{{ $message->excerpt }}</p>
                                </div>

                                {{-- Read status --}}
                                <div class="flex-shrink-0 mt-1">
                                    @if($message->is_read)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Lu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Non lu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($sent->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        {{ $sent->appends(['tab' => 'sent'])->links() }}
                    </div>
                @endif
            @endif
        </div>
    @endif
@endsection
