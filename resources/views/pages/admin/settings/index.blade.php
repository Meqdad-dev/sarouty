@extends('layouts.admin')

@section('title', 'Paramètres du site – Sarouty')
@section('page_title', 'Paramètres du site')
@section('page_subtitle', 'Configurez les options générales de votre plateforme')

@section('top_actions')
    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl panel px-4 py-2.5 text-sm font-medium hover:border-gold/40 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Vider le cache
        </button>
    </form>
@endsection

@section('content')
    {{-- Group Tabs --}}
    <div class="panel rounded-2xl p-2 mb-6">
        <nav class="flex flex-wrap gap-1">
            @foreach($groups as $groupKey => $groupLabel)
                <a href="{{ route('admin.settings.index', ['group' => $groupKey]) }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all
                          {{ $group === $groupKey ? 'bg-gold text-white shadow-lg shadow-gold/30' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    {{ $groupLabel }}
                </a>
            @endforeach
            <a href="{{ route('admin.plans.index') }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                Abonnements
            </a>
        </nav>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('admin.settings.update', ['group' => $group]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="panel rounded-2xl overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center shadow-lg shadow-gold/20">
                            @php
                                $icons = [
                                    'general' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                                    'contact' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                    'social' => 'M13 10V3L4 14h7v7l9-11h-7z',
                                    'seo' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                    'payment' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                    'email' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                    'listing' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                ];
                            @endphp
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$group] ?? $icons['general'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $groups[$group] ?? 'Paramètres' }}</h2>
                            {{-- <p class="text-sm text-gray-500">{{ $settings->count() }} paramètres</p> --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings List --}}
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($settings as $setting)
                    <div class="px-6 py-5 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                            {{-- Label & Description --}}
                            <div class="lg:w-1/3">
                                <label class="block font-medium text-gray-900 dark:text-white">
                                    {{ $setting->label }}
                                </label>
                                @if($setting->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $setting->description }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ $setting->key }}
                                    </span>
                                    @if($setting->is_public)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Public
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Input Field --}}
                            <div class="lg:w-2/3">
                                @switch($setting->type)
                                    @case('text')
                                        <input type="text" 
                                               name="settings[{{ $setting->key }}]" 
                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-gold focus:ring-2 focus:ring-gold/20 transition">
                                    @break

                                    @case('textarea')
                                        <textarea name="settings[{{ $setting->key }}]" 
                                                  rows="3"
                                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-gold focus:ring-2 focus:ring-gold/20 transition resize-none">{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
                                    @break

                                    @case('number')
                                        <input type="number" 
                                               name="settings[{{ $setting->key }}]" 
                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                               class="w-full lg:w-48 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-gold focus:ring-2 focus:ring-gold/20 transition">
                                    @break

                                    @case('boolean')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="1"
                                                   {{ $setting->value == '1' ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/20 dark:bg-gray-600 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-gold"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $setting->value == '1' ? 'Activé' : 'Désactivé' }}
                                            </span>
                                        </label>
                                    @break

                                    @case('image')
                                        <div class="space-y-3">
                                            @if($setting->value)
                                                <div class="flex items-center gap-4">
                                                    <img src="{{ $setting->image_url }}" 
                                                         alt="{{ $setting->label }}"
                                                         class="h-16 rounded-lg border border-gray-200 dark:border-gray-600">
                                                    <button type="button"
                                                            onclick="this.closest('div').querySelector('input[type=file]').click()"
                                                            class="text-sm text-gold hover:text-gold-dark transition">
                                                        Remplacer
                                                    </button>
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="settings[{{ $setting->key }}]"
                                                   accept="image/*"
                                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gold/10 file:text-gold hover:file:bg-gold/20 transition">
                                        </div>
                                    @break

                                    @default
                                        <input type="text" 
                                               name="settings[{{ $setting->key }}]" 
                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-gold focus:ring-2 focus:ring-gold/20 transition">
                                @endswitch

                                @error("settings.{$setting->key}")
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">Aucun paramètre dans cette section</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer Actions --}}
            @if($settings->isNotEmpty())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-end gap-3">
                    <a href="{{ route('admin.settings.index', ['group' => $group]) }}"
                       class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-gold/30 hover:shadow-gold/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>
            @endif
        </div>
    </form>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <div class="panel rounded-2xl p-5 border-l-4 border-blue-500">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Paramètres publics</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Les paramètres marqués "Public" sont accessibles via l'API et peuvent être utilisés sur le frontend.
                    </p>
                </div>
            </div>
        </div>

        <div class="panel rounded-2xl p-5 border-l-4 border-amber-500">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Sensibilité des données</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Les clés secrètes (Stripe, SMTP) sont masquées et ne doivent pas être partagées.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
