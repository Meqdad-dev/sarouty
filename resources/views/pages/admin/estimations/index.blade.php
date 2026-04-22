@extends('layouts.admin')
@section('title', 'Estimations – Sarouty Admin')

@section('content')
<div class="p-6 lg:p-8 space-y-6">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold" style="color:var(--text)">Estimations immobilières</h1>
            <p class="text-sm mt-1" style="color:var(--text-soft)">Demandes reçues depuis le formulaire d'estimation du site</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="sendBulkSelected()" id="btn-bulk"
                    class="hidden items-center gap-2 bg-gold hover:bg-gold-dark text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Envoyer aux sélectionnés
            </button>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total estimations','value'=>number_format($stats['total']),'icon'=>'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z','color'=>'bg-blue-50 text-blue-600'],
            ['label'=>'Avec email','value'=>number_format($stats['with_email']),'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','color'=>'bg-green-50 text-green-600'],
            ['label'=>"Aujourd'hui",'value'=>number_format($stats['today']),'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','color'=>'bg-purple-50 text-purple-600'],
            ['label'=>'Prix moyen estimé','value'=>$stats['avg_price'] ? number_format($stats['avg_price'],0,',',' ').' MAD' : '—','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'bg-amber-50 text-amber-600'],
        ] as $s)
        <div class="panel rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $s['color'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $s['icon'] }}"/></svg>
            </div>
            <div>
                <div class="text-xl font-bold" style="color:var(--text)">{{ $s['value'] }}</div>
                <div class="text-xs" style="color:var(--text-soft)">{{ $s['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filtres ── --}}
    <form method="GET" class="panel rounded-2xl p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, téléphone, ville…"
                       class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold/30" style="border-color:var(--border);background:var(--panel-soft);color:var(--text)">
            </div>
            <select name="type" class="border rounded-xl px-4 py-2.5 text-sm focus:outline-none" style="border-color:var(--border);background:var(--panel-soft);color:var(--text)">
                <option value="">Tous les types</option>
                @foreach(['appartement'=>'Appartement','villa'=>'Villa/Maison','terrain'=>'Terrain','bureau'=>'Bureau','riad'=>'Riad','commerce'=>'Commerce'] as $v=>$l)
                <option value="{{ $v }}" @selected(request('type')===$v)>{{ $l }}</option>
                @endforeach
            </select>
            <select name="transaction" class="border rounded-xl px-4 py-2.5 text-sm focus:outline-none" style="border-color:var(--border);background:var(--panel-soft);color:var(--text)">
                <option value="">Toutes opérations</option>
                @foreach(['vente'=>'Vente','location'=>'Location','neuf'=>'Neuf','vacances'=>'Vacances'] as $v=>$l)
                <option value="{{ $v }}" @selected(request('transaction')===$v)>{{ $l }}</option>
                @endforeach
            </select>
            <select name="city" class="border rounded-xl px-4 py-2.5 text-sm focus:outline-none" style="border-color:var(--border);background:var(--panel-soft);color:var(--text)">
                <option value="">Toutes les villes</option>
                @foreach(['Casablanca','Rabat','Marrakech','Tanger','Agadir','Fès','Meknès','Oujda','El Jadida','Tétouan','Essaouira','Ifrane'] as $city)
                <option value="{{ $city }}" @selected(request('city')===$city)>{{ $city }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 text-sm px-3 py-2.5 border rounded-xl cursor-pointer whitespace-nowrap" style="border-color:var(--border);color:var(--text-soft)">
                <input type="checkbox" name="with_email" value="1" @checked(request('with_email')) class="accent-gold">
                Avec email seulement
            </label>
            <button type="submit" class="bg-gold hover:bg-gold-dark text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all whitespace-nowrap">Filtrer</button>
            @if(request()->hasAny(['search','type','transaction','city','with_email']))
            <a href="{{ route('admin.estimations.index') }}" class="text-sm px-4 py-2.5 rounded-xl border transition-all whitespace-nowrap" style="border-color:var(--border);color:var(--text-soft)">Réinitialiser</a>
            @endif
        </div>
    </form>

    {{-- ── Tableau ── --}}
    <div class="panel rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);background:var(--panel-soft)">
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">
                            <input type="checkbox" id="check-all" onchange="toggleAllCheckboxes(this)" class="accent-gold rounded">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Contact</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Bien</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Ville</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Surface</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Estimation</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Profil</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Date</th>
                        <th class="px-4 py-3 text-left font-semibold" style="color:var(--text-soft)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estimations as $e)
                    <tr class="border-b transition-colors hover:bg-gold/5" style="border-color:var(--border)" id="row-{{ $e->id }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="est-checkbox accent-gold rounded" value="{{ $e->id }}" onchange="updateBulkBtn()">
                        </td>
                        <td class="px-4 py-4">
                            @if($e->contact_name || $e->contact_email || $e->contact_phone)
                            <div class="font-semibold" style="color:var(--text)">{{ $e->contact_name ?: '—' }}</div>
                            @if($e->contact_email)
                            <div class="text-xs mt-0.5" style="color:var(--text-soft)">
                                <a href="mailto:{{ $e->contact_email }}" class="hover:text-gold transition-colors">{{ $e->contact_email }}</a>
                            </div>
                            @endif
                            @if($e->contact_phone)
                            <div class="text-xs mt-0.5" style="color:var(--text-soft)">{{ $e->contact_phone }}</div>
                            @endif
                            @else
                            <span class="text-xs italic" style="color:var(--text-soft)">Anonyme</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @php
                            $typeColors = ['appartement'=>'bg-blue-100 text-blue-700','villa'=>'bg-purple-100 text-purple-700','terrain'=>'bg-green-100 text-green-700','bureau'=>'bg-orange-100 text-orange-700','riad'=>'bg-pink-100 text-pink-700','commerce'=>'bg-red-100 text-red-700'];
                            $txnLabels = ['vente'=>'Vente','location'=>'Location','neuf'=>'Neuf','vacances'=>'Vacances'];
                            @endphp
                            <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $typeColors[$e->property_type] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($e->property_type) }}
                            </span>
                            @if($e->transaction_type)
                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 mt-1">
                                {{ $txnLabels[$e->transaction_type] ?? $e->transaction_type }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 font-medium" style="color:var(--text)">{{ $e->city }}</td>
                        <td class="px-4 py-4" style="color:var(--text-soft)">
                            {{ $e->surface ? number_format($e->surface, 0).' m²' : '—' }}
                        </td>
                        <td class="px-4 py-4">
                            @if($e->estimated_mid)
                            <div class="font-bold text-gold">{{ number_format($e->estimated_mid, 0, ',', ' ') }} MAD</div>
                            <div class="text-xs mt-0.5" style="color:var(--text-soft)">
                                {{ number_format($e->estimated_min, 0, ',', ' ') }} – {{ number_format($e->estimated_max, 0, ',', ' ') }}
                            </div>
                            @else
                            <span style="color:var(--text-soft)">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($e->user_type)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ ucfirst($e->user_type) }}</span>
                            @endif
                            @if($e->wants_professional_help)
                            <div class="text-xs mt-1 text-green-600 font-medium">✓ Aide pro</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-xs whitespace-nowrap" style="color:var(--text-soft)">
                            {{ $e->created_at->diffForHumans() }}<br>
                            <span class="text-xs">{{ $e->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1.5">
                                {{-- Voir détail --}}
                                <a href="{{ route('admin.estimations.show', $e) }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600" style="border-color:var(--border);color:var(--text-soft)"
                                   title="Voir le détail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                {{-- Envoyer email --}}
                                @if($e->contact_email)
                                <button onclick="sendEmail({{ $e->id }}, '{{ addslashes($e->contact_email) }}')"
                                        id="btn-email-{{ $e->id }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors hover:bg-gold/10 hover:border-gold/50 hover:text-gold" style="border-color:var(--border);color:var(--text-soft)"
                                        title="Envoyer un email à {{ $e->contact_email }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </button>
                                @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg border text-gray-300 cursor-not-allowed" style="border-color:var(--border)" title="Pas d'email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                @endif

                                {{-- Supprimer --}}
                                <form action="{{ route('admin.estimations.destroy', $e) }}" method="POST" onsubmit="return confirm('Supprimer cette estimation ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors hover:bg-red-50 hover:border-red-300 hover:text-red-500" style="border-color:var(--border);color:var(--text-soft)" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-gray-500 font-medium">Aucune estimation trouvée</p>
                                <p class="text-gray-400 text-sm">Les estimations apparaîtront ici dès que des visiteurs utiliseront le formulaire.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($estimations->hasPages())
        <div class="px-6 py-4 border-t" style="border-color:var(--border)">
            {{ $estimations->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── Toast notifications ── --}}
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-medium min-w-64">
        <span id="toast-icon" class="text-lg"></span>
        <span id="toast-msg"></span>
    </div>
</div>


{{-- ── Script inline (fonctionne avec navigation AJAX) ── --}}
<script>
(function() {
    // CSRF token
    const CSRF = document.querySelector('meta[name="csrf-token"]')
                 ? document.querySelector('meta[name="csrf-token"]').content
                 : '{{ csrf_token() }}';

    // ─── Envoyer un email individuel ───────────────────────────
    window.sendEmail = async function(id, email) {
        const btn = document.getElementById('btn-email-' + id);
        if (!btn) return;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>';

        try {
            const res = await fetch('/admin/estimations/' + id + '/envoyer-email', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });

            let data;
            try { data = await res.json(); }
            catch(e) { data = { success: false, message: 'Réponse invalide du serveur (HTTP ' + res.status + ')' }; }

            showAdminToast(data.success, data.message || 'Terminé');

            if (data.success) {
                btn.style.color = '#16a34a';
                btn.style.borderColor = '#86efac';
                btn.title = '✅ Email envoyé à ' + email;
            } else {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        } catch(err) {
            showAdminToast(false, 'Erreur réseau : ' + err.message);
            btn.disabled = false;
            btn.innerHTML = original;
        }
    };

    // ─── Sélection multiple ─────────────────────────────────────
    window.toggleAllCheckboxes = function(master) {
        document.querySelectorAll('.est-checkbox').forEach(function(cb) { cb.checked = master.checked; });
        updateBulkBtn();
    };

    window.updateBulkBtn = function() {
        const checked = document.querySelectorAll('.est-checkbox:checked').length;
        const btn = document.getElementById('btn-bulk');
        if (!btn) return;
        if (checked > 0) {
            btn.classList.remove('hidden');
            btn.classList.add('inline-flex');
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Envoyer aux ' + checked + ' sélectionnés';
        } else {
            btn.classList.add('hidden');
            btn.classList.remove('inline-flex');
        }
    };

    // ─── Envoi en masse ─────────────────────────────────────────
    window.sendBulkSelected = async function() {
        const ids = Array.from(document.querySelectorAll('.est-checkbox:checked')).map(function(cb){ return cb.value; });
        if (!ids.length) return;
        if (!confirm('Envoyer un email à ' + ids.length + ' personne(s) ?')) return;

        const btn = document.getElementById('btn-bulk');
        if (btn) { btn.disabled = true; btn.textContent = 'Envoi en cours…'; }

        try {
            const res = await fetch('/admin/estimations/envoyer-email-masse', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ ids: ids })
            });
            let data;
            try { data = await res.json(); }
            catch(e) { data = { success: false, message: 'Réponse invalide (HTTP ' + res.status + ')' }; }
            showAdminToast(data.success, data.message);
        } catch(err) {
            showAdminToast(false, 'Erreur réseau : ' + err.message);
        } finally {
            if (btn) { btn.disabled = false; }
            updateBulkBtn();
        }
    };

    // ─── Toast ──────────────────────────────────────────────────
    window.showAdminToast = function(success, msg) {
        var toast = document.getElementById('toast');
        var inner = document.getElementById('toast-inner');
        var icon  = document.getElementById('toast-icon');
        var text  = document.getElementById('toast-msg');
        if (!toast) return;

        inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-medium'
                        + (success ? ' bg-green-600' : ' bg-red-500');
        icon.textContent = success ? '✅' : '❌';
        text.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(function(){ toast.classList.add('hidden'); }, 6000);
    };
})();
</script>

@endsection
