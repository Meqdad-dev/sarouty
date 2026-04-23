@extends('layouts.admin')
@section('title', 'Détail estimation – Sarouty Admin')
@section('page_title', 'Détail de l\'estimation')
@section('page_subtitle', 'Consultez les caractéristiques et les coordonnées du client')
@section('content')
<div class="p-6 lg:p-8 space-y-6 max-w-4xl mx-auto">

    {{-- Retour --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.estimations.index') }}" class="flex items-center gap-2 text-sm hover:text-gold transition-colors" style="color:var(--text-soft)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour aux estimations
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne gauche : résultats et bien ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Estimation résultat --}}
            <div class="panel rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b" style="border-color:var(--border);background:var(--panel-soft)">
                    <h2 class="font-bold text-base" style="color:var(--text)">Résultat de l'estimation</h2>
                </div>
                <div class="p-6" style="background:linear-gradient(135deg,#1A1410,#2D1F12)">
                    <div class="text-center">
                        <div class="text-white/50 text-sm mb-1">Valeur estimée</div>
                        <div class="font-display text-4xl font-bold text-gold mb-1">
                            {{ $estimation->estimated_mid ? number_format($estimation->estimated_mid, 0, ',', ' ').' MAD' : '—' }}
                        </div>
                        <div class="text-white/40 text-sm">
                            {{ ucfirst($estimation->property_type) }} • {{ $estimation->city }}
                            @if($estimation->surface) • {{ number_format($estimation->surface, 0) }} m²@endif
                        </div>
                    </div>
                    @if($estimation->estimated_min)
                    <div class="grid grid-cols-3 gap-4 mt-5 pt-5 border-t border-white/10 text-center">
                        <div><div class="text-white/40 text-xs mb-0.5">Estimation basse</div><div class="text-white font-semibold text-sm">{{ number_format($estimation->estimated_min, 0, ',', ' ') }} MAD</div></div>
                        <div class="border-x border-white/10"><div class="text-white/40 text-xs mb-0.5">Prix au m²</div><div class="text-gold font-semibold text-sm">{{ $estimation->price_per_sqm ? number_format($estimation->price_per_sqm, 0, ',', ' ').' MAD' : '—' }}</div></div>
                        <div><div class="text-white/40 text-xs mb-0.5">Estimation haute</div><div class="text-white font-semibold text-sm">{{ number_format($estimation->estimated_max, 0, ',', ' ') }} MAD</div></div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Caractéristiques --}}
            <div class="panel rounded-2xl">
                <div class="px-6 py-4 border-b" style="border-color:var(--border);background:var(--panel-soft)">
                    <h2 class="font-bold text-base" style="color:var(--text)">Caractéristiques du bien</h2>
                </div>
                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach([
                        ['Type de bien', ucfirst($estimation->property_type)],
                        ['Opération', ucfirst($estimation->transaction_type ?? '—')],
                        ['Ville', $estimation->city],
                        ['Surface', $estimation->surface ? number_format($estimation->surface, 0).' m²' : '—'],
                        ['Chambres', $estimation->bedrooms ?? '—'],
                        ['Salles de bain', $estimation->bathrooms ?? '—'],
                        ['Étage', $estimation->floor !== null ? ($estimation->floor === 0 ? 'RDC' : $estimation->floor) : '—'],
                        ['Condition', ucfirst(str_replace('_',' ',$estimation->condition ?? '—'))],
                        ['Année construction', $estimation->construction_year ?? '—'],
                    ] as [$label, $value])
                    <div class="rounded-xl p-3" style="background:var(--panel-soft)">
                        <div class="text-xs font-medium mb-1" style="color:var(--text-soft)">{{ $label }}</div>
                        <div class="font-semibold text-sm" style="color:var(--text)">{{ $value }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Équipements --}}
            <div class="panel rounded-2xl">
                <div class="px-6 py-4 border-b" style="border-color:var(--border);background:var(--panel-soft)">
                    <h2 class="font-bold text-base" style="color:var(--text)">Équipements</h2>
                </div>
                <div class="p-6 flex flex-wrap gap-2">
                    @foreach([
                        [$estimation->has_garage  , 'Garage'    , $estimation->garage_places  ? "({$estimation->garage_places} places)" : ''],
                        [$estimation->has_garden  , 'Jardin'    , $estimation->garden_surface  ? number_format($estimation->garden_surface,0).' m²' : ''],
                        [$estimation->has_terrace , 'Terrasse'  , $estimation->terrace_surface ? number_format($estimation->terrace_surface,0).' m²' : ''],
                        [$estimation->has_pool    , 'Piscine'   , ''],
                        [$estimation->has_elevator, 'Ascenseur' , ''],
                        [$estimation->has_parking , 'Parking'   , ''],
                        [$estimation->is_furnished, 'Meublé'    , ''],
                        [$estimation->has_security, 'Sécurité'  , ''],
                    ] as [$has, $name, $detail])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $has ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400 line-through' }}">
                        @if($has)✓@else✗@endif {{ $name }}
                        @if($has && $detail)<span class="font-normal opacity-80">&nbsp;{{ $detail }}</span>@endif
                    </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Colonne droite : contact + profil ── --}}
        <div class="space-y-5">

            {{-- Contact --}}
            <div class="panel rounded-2xl">
                <div class="px-5 py-4 border-b" style="border-color:var(--border);background:var(--panel-soft)">
                    <h2 class="font-bold text-base" style="color:var(--text)">Contact</h2>
                </div>
                <div class="p-5 space-y-3">
                    @if($estimation->contact_name)
                    <div>
                        <div class="text-xs font-medium mb-0.5" style="color:var(--text-soft)">Nom</div>
                        <div class="font-semibold text-sm" style="color:var(--text)">{{ $estimation->contact_name }}</div>
                    </div>
                    @endif
                    @if($estimation->contact_email)
                    <div>
                        <div class="text-xs font-medium mb-0.5" style="color:var(--text-soft)">Email</div>
                        <a href="mailto:{{ $estimation->contact_email }}" class="font-semibold text-sm text-gold hover:underline">{{ $estimation->contact_email }}</a>
                    </div>
                    @endif
                    @if($estimation->contact_phone)
                    <div>
                        <div class="text-xs font-medium mb-0.5" style="color:var(--text-soft)">Téléphone</div>
                        <a href="tel:{{ $estimation->contact_phone }}" class="font-semibold text-sm" style="color:var(--text)">{{ $estimation->contact_phone }}</a>
                    </div>
                    @endif
                    @if(!$estimation->contact_name && !$estimation->contact_email && !$estimation->contact_phone)
                    <p class="text-sm italic" style="color:var(--text-soft)">Aucune information de contact fournie</p>
                    @endif

                    @if($estimation->contact_email)
                    <button onclick="sendEmail({{ $estimation->id }}, '{{ addslashes($estimation->contact_email) }}')"
                            id="btn-email-{{ $estimation->id }}"
                            class="mt-3 w-full flex items-center justify-center gap-2 bg-gold hover:bg-gold-dark text-white font-semibold py-2.5 rounded-xl transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Envoyer l'email d'invitation
                    </button>
                    @endif
                </div>
            </div>

            {{-- Profil utilisateur --}}
            <div class="panel rounded-2xl">
                <div class="px-5 py-4 border-b" style="border-color:var(--border);background:var(--panel-soft)">
                    <h2 class="font-bold text-base" style="color:var(--text)">Profil utilisateur</h2>
                </div>
                <div class="p-5 space-y-3">
                    @foreach([
                        ['Type', ucfirst($estimation->user_type ?? '—')],
                        ['Est propriétaire', $estimation->is_owner ? 'Oui' : 'Non'],
                        ['Aide professionnelle', $estimation->wants_professional_help ? 'Oui ✓' : 'Non'],
                        ['Horizon', match($estimation->timeline) {
                            'maintenant'=>'Immédiatement','3mois'=>'Dans 3 mois','6mois'=>'Dans 6 mois','plus'=>'Plus tard',default=>'—'
                        }],
                    ] as [$label, $value])
                    <div class="flex items-center justify-between">
                        <span class="text-xs" style="color:var(--text-soft)">{{ $label }}</span>
                        <span class="text-xs font-semibold" style="color:var(--text)">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Méta --}}
            <div class="panel rounded-2xl">
                <div class="px-5 py-4 border-b" style="border-color:var(--border);background:var(--panel-soft)">
                    <h2 class="font-bold text-base" style="color:var(--text)">Informations</h2>
                </div>
                <div class="p-5 space-y-2 text-xs" style="color:var(--text-soft)">
                    <div class="flex justify-between"><span>ID</span><span class="font-mono font-semibold">#{{ $estimation->id }}</span></div>
                    <div class="flex justify-between"><span>IP</span><span class="font-mono">{{ $estimation->ip_address ?? '—' }}</span></div>
                    <div class="flex justify-between"><span>Reçu le</span><span>{{ $estimation->created_at->format('d/m/Y à H:i') }}</span></div>
                </div>
            </div>

            {{-- Supprimer --}}
            <form action="{{ route('admin.estimations.destroy', $estimation) }}" method="POST"
                  onsubmit="return confirm('Supprimer cette estimation définitivement ?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 border border-red-200 text-red-500 hover:bg-red-50 font-semibold py-2.5 rounded-xl transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer l'estimation
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-medium min-w-64">
        <span id="toast-icon"></span><span id="toast-msg"></span>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function sendEmail(id, email) {
    const btn = document.getElementById('btn-email-' + id);
    if (!btn) return;
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Envoi en cours…';

    try {
        const res  = await fetch(`/admin/estimations/${id}/envoyer-email`, {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
        });
        const data = await res.json();
        showToast(data.success, data.message);
        if (data.success) btn.innerHTML = '✅ Email envoyé !';
    } catch(e) {
        showToast(false,'Erreur réseau.');
        btn.disabled = false; btn.innerHTML = orig;
    }
}

function showToast(ok, msg) {
    const toast=document.getElementById('toast'),
          inner=document.getElementById('toast-inner'),
          icon=document.getElementById('toast-icon'),
          text=document.getElementById('toast-msg');
    inner.className=`flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-medium min-w-64 ${ok?'bg-green-600':'bg-red-500'}`;
    icon.textContent=ok?'✅':'❌'; text.textContent=msg;
    toast.classList.remove('hidden');
    setTimeout(()=>toast.classList.add('hidden'),5000);
}
</script>
@endpush
@endsection
