@extends('layouts.user')
@php cache()->put('user_viewed_notifications_' . auth()->id(), now()); @endphp

@section('title', 'Notifications – Mon Espace')
@section('page_title', 'Mes notifications')
@section('page_subtitle', 'Restez informé de l\'activité sur vos annonces')

@section('top_actions')
    @if($stats['unread'] > 0)
        <form action="{{ route('user.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold text-white text-sm font-medium hover:bg-gold-dark transition shadow-sm shadow-gold/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Tout marquer comme lu
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="mb-12 max-w-5xl">
    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="panel rounded-[24px] p-6 lg:p-8 flex flex-col justify-center items-center">
            <div class="text-4xl lg:text-5xl font-display font-bold text-gray-900 dark:text-white mb-2">{{ number_format($stats['total']) }}</div>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</div>
        </div>
        <div class="panel rounded-[24px] p-6 lg:p-8 flex flex-col justify-center items-center">
            <div class="text-4xl lg:text-5xl font-display font-bold text-amber-600 dark:text-amber-500 mb-2">{{ number_format($stats['unread']) }}</div>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Non lues</div>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="panel rounded-[24px] overflow-hidden">
        @if($notifications->isEmpty())
            <div class="text-center py-20">
                <div class="w-24 h-24 rounded-full bg-gray-50 dark:bg-gray-800/50 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-2">Aucune notification</h3>
                <p class="text-gray-500 dark:text-gray-400">Vous êtes à jour !</p>
            </div>
        @else
            @foreach($grouped as $date => $groupNotifications)
                <div class="border-b border-gray-100 dark:border-gray-800 last:border-b-0">
                    {{-- Date Header --}}
                    <div class="px-6 lg:px-8 py-3 bg-gray-50/50 dark:bg-gray-800/30">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $date }}</span>
                    </div>

                    {{-- Notifications --}}
                    <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                        @foreach($groupNotifications as $notification)
                            <div class="px-6 lg:px-8 py-5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ !$notification->read ? 'bg-gold/5 dark:bg-gold/10' : '' }}">
                                <div class="flex items-start gap-5">
                                    {{-- Icon --}}
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0
                                        {{ $notification->type === 'listing_approved' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 
                                           ($notification->type === 'new_message' || $notification->type === 'message_reply' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' :
                                           ($notification->type === 'listing_rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400')) }}">
                                        @if($notification->type === 'listing_approved')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @elseif($notification->type === 'new_message' || $notification->type === 'message_reply')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        @elseif($notification->type === 'listing_rejected')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0 py-1">
                                        <p class="font-semibold text-lg mb-1 {{ !$notification->read ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300' }}">{{ $notification->title }}</p>
                                        @if($notification->body)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 leading-relaxed">{{ $notification->body }}</p>
                                        @endif
                                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3 flex-shrink-0">
                                        @if($notification->url)
                                            <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 hover:opacity-90 transition">
                                                    Voir
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif(!$notification->read)
                                            <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                                    Marquer lu
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('user.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- Unread indicator --}}
                                        @if(!$notification->read)
                                            <span class="w-2.5 h-2.5 rounded-full bg-gold flex-shrink-0 shadow-sm shadow-gold/40"></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="px-6 lg:px-8 py-5 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
