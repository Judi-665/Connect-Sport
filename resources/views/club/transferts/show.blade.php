{{-- resources/views/club/transferts/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Transfert — ' . $transfert->joueur->nomComplet())

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('club.dashboard') }}" class="text-warning text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('club.transferts.index') }}" class="text-warning text-decoration-none">Transferts</a>
            </li>
            <li class="breadcrumb-item active">{{ $transfert->joueur->nomComplet() }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $isSource = $transfert->club_source_id === $club->id;
        $sc = match($transfert->statut) {
            'en_attente'     => 'bg-warning text-dark',
            'en_negociation' => 'bg-info text-dark',
            'accepte'        => 'bg-success',
            'refuse'         => 'bg-danger',
            'annule'         => 'bg-secondary',
            default          => 'bg-secondary',
        };
        $sl = match($transfert->statut) {
            'en_attente'     => 'En attente',
            'en_negociation' => 'En négociation',
            'accepte'        => 'Accepté',
            'refuse'         => 'Refusé',
            'annule'         => 'Annulé',
            default          => ucfirst($transfert->statut),
        };
    @endphp

    <div class="row g-4">

        {{-- Colonne principale --}}
        <div class="col-lg-8">

            {{-- Hero joueur --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                 style="width:64px;height:64px;font-size:22px;">
                                {{ mb_substr($transfert->joueur->user->prenom ?? '?', 0, 1) }}{{ mb_substr($transfert->joueur->user->name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <h2 class="h4 fw-bold mb-1">{{ $transfert->joueur->nomComplet() }}</h2>
                                <div class="text-muted small">
                                    <i class="bi bi-person-badge me-1"></i>{{ ucfirst($transfert->joueur->poste ?? '—') }}
                                    @if($transfert->joueur->categorie)
                                        · {{ ucfirst($transfert->joueur->categorie) }}
                                    @endif
                                </div>
                                <div class="mt-1">
                                    @if($isSource)
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill small">
                                            <i class="bi bi-arrow-up-right me-1"></i>Sortant
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill small">
                                            <i class="bi bi-arrow-down-left me-1"></i>Entrant
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="badge {{ $sc }} rounded-pill px-3 py-2 fs-6"
                              id="badge-statut">{{ $sl }}</span>
                    </div>
                </div>
            </div>

            {{-- Infos transfert --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom pt-3 pb-2 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-info-circle text-warning me-2"></i>Informations du transfert
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Type</div>
                            <span class="badge {{ $transfert->type === 'definitif' ? 'bg-primary' : 'bg-info' }} rounded-pill">
                                {{ $transfert->type === 'definitif' ? 'Définitif' : 'Prêt' }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Montant</div>
                            <div class="fw-bold">{{ $transfert->montantAffiche() }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Date d'effet</div>
                            <div class="fw-bold">{{ $transfert->date_effet?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        @if($transfert->type === 'pret' && $transfert->date_fin_pret)
                        <div class="col-md-6">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Fin du prêt</div>
                            <div class="fw-bold">{{ $transfert->date_fin_pret->format('d/m/Y') }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- Clubs --}}
                    <div class="row g-3 mt-2 pt-3 border-top">
                        <div class="col-md-6">
                            <div class="text-muted small fw-semibold text-uppercase mb-2">Club source</div>
                            <div class="d-flex align-items-center gap-2">
                                @if($transfert->clubSource->logo)
                                    <img src="{{ asset('storage/'.$transfert->clubSource->logo) }}"
                                         class="rounded-2 object-fit-cover flex-shrink-0"
                                         style="width:36px;height:36px;" alt="">
                                @else
                                    <div class="rounded-2 bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:36px;height:36px;">
                                        <i class="bi bi-shield-check text-warning"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold small">{{ $transfert->clubSource->nom }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $transfert->clubSource->ville ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-semibold text-uppercase mb-2">Club destinataire</div>
                            <div class="d-flex align-items-center gap-2">
                                @if($transfert->clubDestinataire->logo)
                                    <img src="{{ asset('storage/'.$transfert->clubDestinataire->logo) }}"
                                         class="rounded-2 object-fit-cover flex-shrink-0"
                                         style="width:36px;height:36px;" alt="">
                                @else
                                    <div class="rounded-2 bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:36px;height:36px;">
                                        <i class="bi bi-shield-check text-warning"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold small">{{ $transfert->clubDestinataire->nom }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $transfert->clubDestinataire->ville ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agent --}}
            @if($transfert->agent)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom pt-3 pb-2 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-fill-gear text-warning me-2"></i>Agent sportif
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:46px;height:46px;font-size:16px;">
                            {{ mb_substr($transfert->agent->user->prenom ?? 'A', 0, 1) }}{{ mb_substr($transfert->agent->user->name ?? '', 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $transfert->agent->user->prenom ?? '' }} {{ $transfert->agent->user->name ?? '' }}</div>
                            <div class="text-muted small">{{ $transfert->agent->user->email ?? '—' }}</div>
                        </div>
                        <a href="{{ route('messages.inbox') }}"
                           class="btn btn-outline-primary btn-sm rounded-pill">
                            <i class="bi bi-chat me-1"></i> Contacter
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Notes --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom pt-3 pb-2 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-chat-quote text-warning me-2"></i>Notes & Observations
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($transfert->note_joueur)
                        <div class="mb-3">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Note du joueur</div>
                            <p class="mb-0">{{ $transfert->note_joueur }}</p>
                        </div>
                    @endif
                    @if($transfert->note_club_source)
                        <div class="mb-3 {{ $transfert->note_joueur ? 'pt-3 border-top' : '' }}">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Note — club source</div>
                            <p class="mb-0">{{ $transfert->note_club_source }}</p>
                        </div>
                    @endif
                    @if($transfert->note_club_destinataire)
                        <div class="{{ ($transfert->note_joueur || $transfert->note_club_source) ? 'pt-3 border-top' : '' }}">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Note — club destinataire</div>
                            <p class="mb-0">{{ $transfert->note_club_destinataire }}</p>
                        </div>
                    @endif
                    @if(!$transfert->note_joueur && !$transfert->note_club_source && !$transfert->note_club_destinataire)
                        <p class="text-muted text-center mb-0 py-2 small">Aucune note pour le moment.</p>
                    @endif

                    {{-- Ajouter une note si en cours --}}
                    @if($transfert->estEnCours())
                        <div class="mt-4 pt-3 border-top">
                            <div class="text-muted small fw-semibold text-uppercase mb-2">Ajouter une note</div>
                            <textarea id="noteTexte" class="form-control rounded-3 mb-2" rows="3"
                                      placeholder="Votre note sur ce transfert…"></textarea>
                            <button type="button" id="btnNote"
                                    class="btn btn-outline-warning rounded-pill btn-sm fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Enregistrer la note
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="col-lg-4">

            {{-- Actions --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom pt-3 pb-2 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-lightning text-warning me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-2">

                    @if($transfert->estEnCours())

                        {{-- Source : modifier si en_attente --}}
                        @if($isSource && $transfert->statut === 'en_attente')
                            <a href="{{ route('club.transferts.edit', $transfert) }}"
                               class="btn btn-outline-warning rounded-pill fw-semibold">
                                <i class="bi bi-pencil me-1"></i> Modifier
                            </a>
                        @endif

                        {{-- Démarrer négociation --}}
                        @if($transfert->statut === 'en_attente')
                            <button type="button" class="btn btn-outline-info rounded-pill btn-action"
                                    data-statut="en_negociation"
                                    data-confirm="Démarrer la négociation ?">
                                <i class="bi bi-chat-dots me-1"></i> Démarrer la négociation
                            </button>
                        @endif

                        {{-- Destinataire : accepter --}}
                        @if(!$isSource)
                            <button type="button" class="btn btn-warning rounded-pill fw-semibold btn-action"
                                    data-statut="accepte"
                                    data-confirm="Accepter ce transfert définitivement ?">
                                <i class="bi bi-check-lg me-1"></i> Accepter le transfert
                            </button>
                        @endif

                        {{-- Refuser --}}
                        <button type="button" class="btn btn-outline-danger rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#refusModal">
                            <i class="bi bi-x-lg me-1"></i> Refuser
                        </button>

                        {{-- Annuler (source) --}}
                        @if($isSource)
                            <button type="button" class="btn btn-outline-secondary rounded-pill btn-action"
                                    data-statut="annule"
                                    data-confirm="Annuler ce transfert ?">
                                <i class="bi bi-slash-circle me-1"></i> Annuler
                            </button>
                        @endif

                    @else
                        <p class="text-muted small text-center mb-0">
                            Ce transfert est <strong>{{ $sl }}</strong> — aucune action disponible.
                        </p>
                    @endif

                    <a href="{{ route('club.transferts.index') }}"
                       class="btn btn-light border rounded-pill mt-1">
                        <i class="bi bi-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>

            {{-- Historique --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom pt-3 pb-2 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-clock-history text-warning me-2"></i>Historique
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex gap-3 mb-3">
                        <div class="rounded-circle bg-primary flex-shrink-0"
                             style="width:10px;height:10px;margin-top:5px;"></div>
                        <div>
                            <div class="fw-semibold small">Demande créée</div>
                            <div class="text-muted" style="font-size:11px;">
                                {{ $transfert->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>

                    @if($transfert->statut === 'en_negociation' || $transfert->estFinalise())
                        <div class="d-flex gap-3 mb-3">
                            <div class="rounded-circle bg-info flex-shrink-0"
                                 style="width:10px;height:10px;margin-top:5px;"></div>
                            <div>
                                <div class="fw-semibold small">Négociation engagée</div>
                                <div class="text-muted" style="font-size:11px;">
                                    {{ $transfert->updated_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($transfert->estFinalise())
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-success flex-shrink-0"
                                 style="width:10px;height:10px;margin-top:5px;"></div>
                            <div>
                                <div class="fw-semibold small">Finalisé</div>
                                <div class="text-muted" style="font-size:11px;">
                                    {{ $transfert->finalise_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(in_array($transfert->statut, ['refuse','annule']))
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-danger flex-shrink-0"
                                 style="width:10px;height:10px;margin-top:5px;"></div>
                            <div>
                                <div class="fw-semibold small">{{ $sl }}</div>
                                <div class="text-muted" style="font-size:11px;">
                                    {{ $transfert->updated_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
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
const transfertId = {{ $transfert->id }};

const badgeMap = {
    en_attente:     { cls: 'bg-warning text-dark',  label: 'En attente' },
    en_negociation: { cls: 'bg-info text-dark',     label: 'En négociation' },
    accepte:        { cls: 'bg-success',             label: 'Accepté' },
    refuse:         { cls: 'bg-danger',              label: 'Refusé' },
    annule:         { cls: 'bg-secondary',           label: 'Annulé' },
};

async function updateStatut(statut, note = null) {
    const body = { statut };
    if (note) body.note = note;

    const res = await fetch(`/club/transferts/${transfertId}/statut`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    });

    if (!res.ok) { alert('Erreur lors de la mise à jour.'); return false; }

    const badge = document.getElementById('badge-statut');
    if (badge && badgeMap[statut]) {
        badge.className = `badge ${badgeMap[statut].cls} rounded-pill px-3 py-2 fs-6`;
        badge.textContent = badgeMap[statut].label;
    }

    return true;
}

/* Actions boutons */
document.querySelectorAll('.btn-action').forEach(btn => {
    btn.addEventListener('click', async function () {
        if (this.dataset.confirm && !confirm(this.dataset.confirm)) return;
        this.disabled = true;
        const ok = await updateStatut(this.dataset.statut);
        if (ok) location.reload();
        else this.disabled = false;
    });
});

/* Note */
const btnNote = document.getElementById('btnNote');
if (btnNote) {
    btnNote.addEventListener('click', async function () {
        const note = document.getElementById('noteTexte').value.trim();
        if (!note) { alert('Veuillez saisir une note.'); return; }
        this.disabled = true;
        const res = await fetch(`/club/transferts/${transfertId}/statut`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ statut: '{{ $transfert->statut }}', note }),
        });
        if (res.ok) location.reload();
        else { alert('Erreur.'); this.disabled = false; }
    });
}

/* Refus */
document.getElementById('btnRefusConfirm').addEventListener('click', async function () {
    const note = document.getElementById('refusNote').value.trim();
    if (!note) { document.getElementById('refusError').classList.remove('d-none'); return; }
    this.disabled = true;
    const ok = await updateStatut('refuse', note);
    if (ok) {
        bootstrap.Modal.getInstance(document.getElementById('refusModal')).hide();
        location.reload();
    } else this.disabled = false;
});
</script>
@endpush
@endsection