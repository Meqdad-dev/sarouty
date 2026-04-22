@extends('layouts.admin')

@section('title', 'Gestion des Abonnements')
@section('page_title', 'Gestion des Abonnements')

@section('top_actions')
<a href="{{ route('admin.plans.create') }}" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-lg shadow-gold/30 hover:shadow-gold/50">
    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Ajouter un Abonnement
</a>
@endsection

@section('content')
    {{-- Group Tabs (Shared with Settings) --}}
    <div class="panel rounded-2xl p-2 mb-6">
        <nav class="flex flex-wrap gap-1">
            @php
                $groups = \App\Models\Setting::GROUPS;
            @endphp
            @foreach($groups as $groupKey => $groupLabel)
                <a href="{{ route('admin.settings.index', ['group' => $groupKey]) }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                    {{ $groupLabel }}
                </a>
            @endforeach
            <a href="{{ route('admin.plans.index') }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all bg-gold text-white shadow-lg shadow-gold/30">
                Abonnements
            </a>
        </nav>
    </div>

<div class="panel p-6 rounded-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="text-gray-500 border-b border-gray-100 dark:border-gray-800">
                    <th class="pb-3 font-medium">Nom / Slug</th>
                    <th class="pb-3 font-medium text-center">Niveau</th>
                    <th class="pb-3 font-medium">Prix (MAD)</th>
                    <th class="pb-3 font-medium">Annonces (Part/Pro)</th>
                    <th class="pb-3 font-medium">Photos max</th>
                    <th class="pb-3 font-medium text-center">Statut</th>
                    <th class="pb-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($plans as $plan)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <td class="py-4">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $plan->name }}</div>
                        <div class="text-xs text-gray-500">{{ $plan->slug }}</div>
                    </td>
                    <td class="py-4 text-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-300">
                            {{ $plan->priority_level }}
                        </span>
                    </td>
                    <td class="py-4 font-semibold text-gold">
                        {{ number_format($plan->price, 0, ',', ' ') }}
                        @if($plan->duration_days)
                            <span class="text-xs font-normal text-gray-500"> / {{ $plan->duration_days }}j</span>
                        @endif
                    </td>
                    <td class="py-4">
                        {{ $plan->max_ads_particulier }} <span class="text-gray-400">/</span> {{ $plan->max_ads_agent }}
                    </td>
                    <td class="py-4">
                        {{ $plan->max_images_particulier }} <span class="text-gray-400">/</span> {{ $plan->max_images_agent }}
                    </td>
                    <td class="py-4 text-center">
                        @if($plan->is_active)
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 rounded-lg text-xs font-medium">Actif</span>
                        @else
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400 rounded-lg text-xs font-medium">Inactif</span>
                        @endif
                    </td>
                    <td class="py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="p-2 text-gray-400 hover:text-blue-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            @if($plan->slug !== 'gratuit')
                                <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet abonnement ?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
