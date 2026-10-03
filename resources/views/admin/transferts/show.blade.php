{{-- resources/views/admin/transferts/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails Transfert — ' . ($transfert->joueur?->nomComplet() ?? 'Transfert'))

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 24px; }
.cs-card__header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.cs-card__body { padding: 20px; }
@media (max-width: 768px) { .cs-dash__main { margin-left: 0; } .cs-dash__content { padding: 16px; } }
</style>
@endpush

@section('content')
<div class="cs-dash">
    @include('admin.partials._sidebar')

    <main class="cs-dash__main">
        <div class="cs-dash__content">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transferts') }}" class="text-decoration-none">Transferts</a></li>
                    <li class="breadcrumb-item active">Dossier #{{ $transfert->id }}</li>
                </ol>
            </nav>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Fiche Transfert --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <div>
                                <h4 class="fw-bold mb-0" style="color:var(--text);">Transfert de {{ $transfert->joueur?->nomComplet() ?? 'Joueur' }}</h4>
                                <span class="text-muted small">Créé le {{ $transfert->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                {{ ucfirst(str_replace('_', ' ', $transfert->statut)) }}
                            </span>
                        </div>
                        <div class="cs-card__body">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background:var(--bg2);border:1px solid var(--border);">
                                <div class="text-center flex-grow-1">
                                    <div class="text-muted small text-uppercase">Club Source</div>
                                    <h6 class="fw-bold mb-0">{{ $transfert->clubSource?->nom ?? 'Joueur Libre' }}</h6>
                                </div>
                                <div class="px-3 text-muted fs-4"><i class="bi bi-arrow-right"></i></div>
                                <div class="text-center flex-grow-1">
                                    <div class="text-muted small text-uppercase">Club Destinataire</div>
                                    <h6 class="fw-bold mb-0">{{ $transfert->clubDestinataire?->nom ?? 'Non déterminé' }}</h6>
                                </div>
                            </div>

                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted small text-uppercase">Montant convenu</dt>
                                <dd class="col-sm-8 fw-bold text-success fs-5">
                                    {{ $transfert->montant ? number_format($transfert->montant, 0, ',', ' ') . ' ' . ($transfert->devise ?? 'FCFA') : 'Gratuit' }}
                                </dd>

                                <dt class="col-sm-4 text-muted small text-uppercase">Type de contrat</dt>
                                <dd class="col-sm-8">{{ ucfirst($transfert->type ?? 'Définitif') }}</dd>

                                <dt class="col-sm-4 text-muted small text-uppercase">Agent impliqué</dt>
                                <dd class="col-sm-8">{{ $transfert->agent?->nomComplet() ?? 'Aucun agent' }} ({{ $transfert->agent?->agence ?? '-' }})</dd>

                                <dt class="col-sm-4 text-muted small text-uppercase">Date d'effet</dt>
                                <dd class="col-sm-8">{{ $transfert->date_effet ? $transfert->date_effet->format('d/m/Y') : 'Immédiate' }}</dd>

                                @if($transfert->finalise_at)
                                <dt class="col-sm-4 text-muted small text-uppercase">Finalisé le</dt>
                                <dd class="col-sm-8">{{ $transfert->finalise_at->format('d/m/Y à H:i') }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    {{-- Notes et historique des parties --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-chat-left-text me-2"></i>Notes & Remarques du dossier</h6>
                        </div>
                        <div class="cs-card__body">
                            @if($transfert->note_club_source)
                                <div class="mb-3">
                                    <strong>Remarque Club Départ :</strong>
                                    <div class="p-2 rounded bg-light small mt-1">{{ $transfert->note_club_source }}</div>
                                </div>
                            @endif

                            @if($transfert->note_club_destinataire)
                                <div class="mb-3">
                                    <strong>Remarque Club Arrivée / Arbitrage :</strong>
                                    <div class="p-2 rounded bg-light small mt-1">{{ $transfert->note_club_destinataire }}</div>
                                </div>
                            @endif

                            @if(!$transfert->note_club_source && !$transfert->note_club_destinataire)
                                <p class="text-muted small mb-0">Aucune note particulière sur ce transfert.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Colonne droite : Pouvoirs d'arbitrage de l'Admin --}}
                <div class="col-lg-4">
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-shield-shaded me-2"></i>Arbitrage Administrateur</h6>
                        </div>
                        <div class="cs-card__body d-grid gap-3">
                            <p class="small text-muted mb-0">
                                En cas de litige, désaccord ou blocage d'une partie, l'administrateur dispose du plein pouvoir pour trancher et forcer la finalisation ou l'annulation.
                            </p>

                            {{-- Validation de force --}}
                            <form method="POST" action="{{ route('admin.transferts.valider', $transfert) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Motif de validation forcée</label>
                                    <input type="text" name="motif" class="form-control form-control-sm rounded-3" placeholder="Ex: Décision de commission d'arbitrage...">
                                </div>
                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold" onsubmit="return confirm('Confirmer la validation forcée ?');">
                                    <i class="bi bi-check-circle me-1"></i> Valider de force
                                </button>
                            </form>

                            <hr class="my-1">

                            {{-- Rejet de force --}}
                            <form method="POST" action="{{ route('admin.transferts.rejeter', $transfert) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Motif de rejet / annulation forcée</label>
                                    <input type="text" name="motif" class="form-control form-control-sm rounded-3" placeholder="Ex: Non-respect des règlements de mutation...">
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-semibold" onsubmit="return confirm('Confirmer l\'annulation / rejet forcé ?');">
                                    <i class="bi bi-x-circle me-1"></i> Annuler / Rejeter de force
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
