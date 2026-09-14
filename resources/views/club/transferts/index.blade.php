{{-- resources/views/club/transferts/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Transferts — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Transferts</h1>
            <p class="text-muted small mb-0">{{ $club->nom }}</p>
        </div>
        <a href="{{ route('club.transferts.create') }}" class="btn btn-warning rounded-pill fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Proposer un transfert
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Compteurs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $stats['en_attente'] }}</div>
                    <div class="small text-muted text-uppercase fw-semibold">En attente</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-info">{{ $stats['en_cours'] }}</div>
                    <div class="small text-muted text-uppercase fw-semibold">En négociation</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-success">{{ $stats['acceptes'] }}</div>
                    <div class="small text-muted text-uppercase fw-semibold">Acceptés</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-2 fw-bold text-secondary">{{ $stats['definitifs'] }}</div>
                    <div class="small text-muted text-uppercase fw-semibold">Définitifs</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('club.transferts.index') }}" class="mb-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Statut</label>
                    <select name="statut" class="form-select rounded-3">
                        <option value="">Tous</option>
                        <option value="en_attente"     @selected(request('statut')==='en_attente')>En attente</option>
                        <option value="en_negociation" @selected(request('statut')==='en_negociation')>En négociation</option>
                        <option value="accepte"        @selected(request('statut')==='accepte')>Accepté</option>
                        <option value="refuse"         @selected(request('statut')==='refuse')>Refusé</option>
                        <option value="annule"         @selected(request('statut')==='annule')>Annulé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Type</label>
                    <select name="type" class="form-select rounded-3">
                        <option value="">Tous</option>
                        <option value="definitif" @selected(request('type')==='definitif')>Définitif</option>
                        <option value="pret"      @selected(request('type')==='pret')>Prêt</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-uppercase text-muted">Direction</label>
                    <select name="direction" class="form-select rounded-3">
                        <option value="">Tous</option>
                        <option value="sortant" @selected(request('direction')==='sortant')>Sortants</option>
                        <option value="entrant" @selected(request('direction')==='entrant')>Entrants</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-warning rounded-pill fw-semibold w-100">
                        <i class="bi bi-search me-1"></i> Filtrer
                    </button>
                    @if(request()->hasAny(['statut','type','direction']))
                        <a href="{{ route('club.transferts.index') }}"
                           class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-repeat"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Liste --}}
    @forelse($transferts as $t)
        @php
            $isSource = $t->club_source_id === $club->id;
            $autreClub = $isSource ? $t->clubDestinataire : $t->clubSource;
            $sc = match($t->statut) {
                'en_attente'     => 'bg-warning text-dark',
                'en_negociation' => 'bg-info text-dark',
                'accepte'        => 'bg-success',
                'refuse'         => 'bg-danger',
                'annule'         => 'bg-secondary',
                default          => 'bg-secondary',
            };
            $sl = match($t->statut) {
                'en_attente'     => 'En attente',
                'en_negociation' => 'En négociation',
                'accepte'        => 'Accepté',
                'refuse'         => 'Refusé',
                'annule'         => 'Annulé',
                default          => ucfirst($t->statut),
            };
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-3" id="card-{{ $t->id }}">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">

                    {{-- Joueur --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:52px;height:52px;font-size:18px;">
                            {{ mb_substr($t->joueur->user->prenom ?? '?', 0, 1) }}{{ mb_substr($t->joueur->user->name ?? '', 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $t->joueur->nomComplet() }}</div>
                            <div class="text-muted small">
                                {{ ucfirst($t->joueur->poste ?? '—') }}
                                @if($isSource)
                                    · <i class="bi bi-arrow-up-right text-danger"></i> vers <strong>{{ $autreClub->nom ?? '—' }}</strong>
                                @else
                                    · <i class="bi bi-arrow-down-left text-success"></i> depuis <strong>{{ $autreClub->nom ?? '—' }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Statut + badge direction --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($isSource)
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Sortant</span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Entrant</span>
                        @endif
                        <span class="badge {{ $sc }} rounded-pill" id="badge-{{ $t->id }}">{{ $sl }}</span>
                    </div>
                </div>

                {{-- Détails --}}
                <div class="row g-3 border-top pt-3 mb-3">
                    <div class="col-6 col-md-2">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Type</div>
                        <span class="badge {{ $t->type === 'definitif' ? 'bg-primary' : 'bg-info' }} bg-opacity-10 {{ $t->type === 'definitif' ? 'text-primary' : 'text-info' }} rounded-pill">
                            {{ $t->type === 'definitif' ? 'Définitif' : 'Prêt' }}
                        </span>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Montant</div>
                        <div class="small fw-semibold">{{ $t->montantAffiche() }}</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Date effet</div>
                        <div class="small fw-semibold">{{ $t->date_effet?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    @if($t->type === 'pret' && $t->date_fin_pret)
                    <div class="col-6 col-md-2">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Fin prêt</div>
                        <div class="small fw-semibold">{{ $t->date_fin_pret->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($t->agent)
                    <div class="col-6 col-md-2">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Agent</div>
                        <div class="small fw-semibold">{{ $t->agent->user->name ?? '—' }}</div>
                    </div>
                    @endif
                    @if($t->estFinalise())
                    <div class="col-6 col-md-2">
                        <div class="text-muted small fw-semibold text-uppercase mb-1">Finalisé</div>
                        <div class="small text-success fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>{{ $t->finalise_at->format('d/m/Y') }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('club.transferts.show', $t) }}"
                       class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-eye me-1"></i> Détails
                    </a>

                    @if($t->estEnCours())
                        {{-- Source : peut modifier si en_attente --}}
                        @if($isSource && $t->statut === 'en_attente')
                            <a href="{{ route('club.transferts.edit', $t) }}"
                               class="btn btn-sm btn-outline-warning rounded-pill">
                                <i class="bi bi-pencil me-1"></i> Modifier
                            </a>
                        @endif

                        {{-- Démarrer négociation si en_attente --}}
                        @if($t->statut === 'en_attente')
                            <button type="button"
                                    class="btn btn-sm btn-outline-info rounded-pill btn-statut"
                                    data-id="{{ $t->id }}" data-statut="en_negociation">
                                <i class="bi bi-chat-dots me-1"></i> Négocier
                            </button>
                        @endif

                        {{-- Destinataire : accepter --}}
                        @if(!$isSource)
                            <button type="button"
                                    class="btn btn-sm btn-warning rounded-pill fw-semibold btn-statut"
                                    data-id="{{ $t->id }}" data-statut="accepte"
                                    data-confirm="Accepter ce transfert définitivement ?">
                                <i class="bi bi-check-lg me-1"></i> Accepter
                            </button>
                        @endif

                        {{-- Refuser --}}
                        <button type="button"
                                class="btn btn-sm btn-outline-danger rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#refusModal"
                                data-id="{{ $t->id }}"
                                data-joueur="{{ $t->joueur->nomComplet() }}">
                            <i class="bi bi-x-lg me-1"></i> Refuser
                        </button>

                        {{-- Annuler (source seulement) --}}
                        @if($isSource)
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-pill btn-statut ms-auto"
                                    data-id="{{ $t->id }}" data-statut="annule"
                                    data-confirm="Annuler ce transfert ?">
                                <i class="bi bi-slash-circle me-1"></i> Annuler
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-arrow-left-right fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Aucun transfert</h5>
                <p class="text-muted small mb-3">Aucun transfert ne correspond à vos critères.</p>
                <a href="{{ route('club.transferts.create') }}" class="btn btn-warning rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Proposer un transfert
                </a>
            </div>
        </div>
    @endforelse

    @if($transferts->hasPages())
        <div class="mt-3">{{ $transferts->links() }}</div>
    @endif
</div>

{{-- Modal refus --}}
<div class="modal fade" id="refusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Refuser le transfert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Joueur : <strong id="refusJoueur"></strong></p>
                <label class="form-label fw-semibold">Motif <span class="text-danger">*</span></label>
                <textarea id="refusNote" class="form-control rounded-3" rows="3"
                          placeholder="Expliquez la raison du refus…"></textarea>
                <div id="refusError" class="text-danger small mt-1 d-none">Ce champ est requis.</div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="btnRefusConfirm"
                        class="btn btn-danger rounded-pill px-4">Refuser</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

const badgeMap = {
    en_attente:     { cls: 'bg-warning text-dark',  label: 'En attente' },
    en_negociation: { cls: 'bg-info text-dark',     label: 'En négociation' },
    accepte:        { cls: 'bg-success',             label: 'Accepté' },
    refuse:         { cls: 'bg-danger',              label: 'Refusé' },
    annule:         { cls: 'bg-secondary',           label: 'Annulé' },
};

async function updateStatut(id, statut, note = null) {
    const body = { statut };
    if (note) body.note = note;

    const res = await fetch(`/club/transferts/${id}/statut`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    });

    if (!res.ok) { alert('Erreur lors de la mise à jour.'); return false; }

    const badge = document.getElementById(`badge-${id}`);
    if (badge && badgeMap[statut]) {
        badge.className = `badge ${badgeMap[statut].cls} rounded-pill`;
        badge.textContent = badgeMap[statut].label;
    }

    if (['accepte','refuse','annule'].includes(statut)) {
        document.querySelectorAll(`#card-${id} .btn-statut,
            #card-${id} [data-bs-target="#refusModal"]`).forEach(b => b.remove());
    }

    return true;
}

document.querySelectorAll('.btn-statut').forEach(btn => {
    btn.addEventListener('click', async function () {
        if (this.dataset.confirm && !confirm(this.dataset.confirm)) return;
        this.disabled = true;
        await updateStatut(this.dataset.id, this.dataset.statut);
        this.disabled = false;
    });
});

let refusId = null;
document.getElementById('refusModal').addEventListener('show.bs.modal', e => {
    refusId = e.relatedTarget.dataset.id;
    document.getElementById('refusJoueur').textContent = e.relatedTarget.dataset.joueur;
    document.getElementById('refusNote').value = '';
    document.getElementById('refusError').classList.add('d-none');
});

document.getElementById('btnRefusConfirm').addEventListener('click', async function () {
    const note = document.getElementById('refusNote').value.trim();
    if (!note) { document.getElementById('refusError').classList.remove('d-none'); return; }
    this.disabled = true;
    const ok = await updateStatut(refusId, 'refuse', note);
    if (ok) bootstrap.Modal.getInstance(document.getElementById('refusModal')).hide();
    this.disabled = false;
});
</script>
@endpush
@endsection