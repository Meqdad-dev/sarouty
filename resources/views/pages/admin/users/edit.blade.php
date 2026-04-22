@extends('layouts.app')
@section('title', 'Modifier utilisateur – Admin')

@section('content')
<div class="pt-20 min-h-screen bg-gray-50">
    <div class="bg-ink py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div>
                <div class="text-gold text-xs font-semibold uppercase tracking-widest mb-1">Administration</div>
                <h1 class="font-display text-2xl font-bold text-white">Modifier : {{ $user->name }}</h1>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-white/50 hover:text-white text-sm">← Retour</a>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 bg-gray-50 rounded-xl border-0 focus:ring-2 focus:ring-gold/30 text-gray-900 @error('name') ring-2 ring-red-400 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 bg-gray-50 rounded-xl border-0 focus:ring-2 focus:ring-gold/30 text-gray-900 @error('email') ring-2 ring-red-400 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-3 bg-gray-50 rounded-xl border-0 focus:ring-2 focus:ring-gold/30 text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Rôle</label>
                        <select name="role" required
                                class="w-full px-4 py-3 bg-gray-50 rounded-xl border-0 focus:ring-2 focus:ring-gold/30 text-gray-900">
                            <option value="particulier" {{ old('role', $user->role) === 'particulier' ? 'selected' : '' }}>Particulier</option>
                            <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>Agent immobilier</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrateur</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-gold focus:ring-gold">
                        <span class="text-sm font-medium text-gray-700">Compte actif</span>
                    </label>
                </div>

                <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}"
                       class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                            class="bg-gold hover:bg-gold-dark text-white font-semibold px-8 py-2.5 rounded-xl transition-colors">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
