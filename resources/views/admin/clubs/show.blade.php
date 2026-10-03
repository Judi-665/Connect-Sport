{{-- resources/views/admin/clubs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails Club — ' . $club->nom)

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 24px; }
.cs-card__header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.cs-card__body { padding: 20px; }
.cs-card__body--flush { padding: 0; }
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.clubs') }}" class="text-decoration-none">Clubs</a></li>
                    <li class="breadcrumb-item active">{{ $club->nom }}</li>
                </ol>
            </nav>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Fiche d'en-tête du club --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:52px;height:52px;background:#F97316;font-size:18px;">
                            {{ strtoupper(substr($club->nom, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0" style="color:var(--text);">{{ $club->nom }}</h3>
                            <span class="text-muted small">{{ $club->ville }}, {{ $club->pays }} · Créé le {{ $club->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $club->actif ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                            {{ $club->actif ? 'Club Actif' : 'Club Inactif' }}
                        </span>
                        <form method="POST" action="{{ route('admin.clubs.toggle', $club) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-{{ $club->actif ? 'warning' : 'success' }} rounded-pill px-3">
                                {{ $club->actif ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="cs-card__body">
                    <div class="row g-4">
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Compte Utilisateur</span>
                            <strong>{{ $club->user->name ?? '-' }} ({{ $club->user->email ?? 'Non lié' }})</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Téléphone</span>
                            <strong>{{ $club->telephone ?? $club->user->telephone ?? 'Non renseigné' }}</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Stade</span>
                            <strong>{{ $club->stade ?? 'Non renseigné' }}</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Effectif</span>
                            <strong>{{ $club->joueurs->count() }} Joueurs · {{ $club->equipes->count() }} Équipes</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grille détaillée : Joueurs & Licences | Abonnements & Sponsors --}}
            <div class="row g-4">
                {{-- Colonne gauche --}}
                <div class="col-lg-7">
                    {{-- Effectif joueurs du club --}}
                    <div class="cs-card mb-4">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-warning"></i>Joueurs du club ({{ $club->joueurs->count() }})</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Joueur</th>
                                            <th>Poste</th>
                                            <th>Catégorie</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($club->joueurs as $j)
                                        <tr>
                                            <td class="ps-3 fw-semibold">
                                                {{ $j->nomComplet() }}
                                            </td>
                                            <td>{{ $j->poste ?? '-' }}</td>
                                            <td>{{ $j->categorie ?? '-' }}</td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('admin.joueurs.show', $j) }}" class="btn btn-xs btn-light rounded-circle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="p-3 text-center text-muted">Aucun joueur dans ce club.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Licences délivrées --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Licences & Validité ({{ $club->licences->count() }})</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">N° Licence</th>
                                            <th>Joueur</th>
                                            <th>Expiration</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($club->licences as $lic)
                                        <tr>
                                            <td class="ps-3 fw-semibold">{{ $lic->numero_licence }}</td>
                                            <td>{{ $lic->joueur?->nomComplet() ?? 'Joueur' }}</td>
                                            <td>{{ $lic->date_expiration ? $lic->date_expiration->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($lic->estExpiree())
                                                    <span class="badge bg-danger rounded-pill">Expirée</span>
                                                @else
                                                    <span class="badge bg-success rounded-pill">Valide</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="p-3 text-center text-muted">Aucune licence enregistrée.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Colonne droite --}}
                <div class="col-lg-5">
                    {{-- Abonnements et paiements FedaPay du club --}}
                    <div class="cs-card mb-4">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-credit-card me-2 text-success"></i>Historique Abonnements</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            @forelse($club->abonnements as $ab)
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold" style="color:var(--text);">Plan {{ ucfirst($ab->plan ?? 'Standard') }}</span>
                                    <span class="badge {{ $ab->statut === 'actif' ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                        {{ ucfirst($ab->statut) }}
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    Montant : <strong>{{ number_format($ab->montant, 0, ',', ' ') }} {{ $ab->devise ?? 'FCFA' }}</strong>
                                </div>
                                <div class="text-muted small">
                                    Réf FedaPay : <code>{{ $ab->fedapay_transaction_id ?? 'N/A' }}</code>
                                </div>
                                <div class="text-muted small">
                                    Période : {{ $ab->debut_at ? $ab->debut_at->format('d/m/Y') : '-' }} au {{ $ab->fin_at ? $ab->fin_at->format('d/m/Y') : 'Illimité' }}
                                </div>
                            </div>
                            @empty
                            <div class="p-3 text-center text-muted small">Aucun historique de paiement pour ce club.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Sponsors du club --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Sponsors Partenaires ({{ $club->sponsors->count() }})</h6>
                        </div>
                        <div class="cs-card__body">
                            @forelse($club->sponsors as $sp)
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold small" style="color:var(--text);">{{ $sp->nom }}</div>
                                    <div class="text-muted small">{{ $sp->type ?? 'Sponsor' }}</div>
                                </div>
                                <span class="badge bg-light text-dark border rounded-pill">{{ $sp->montant ? number_format($sp->montant, 0, ',', ' ') . ' FCFA' : 'Partenaire' }}</span>
                            </div>
                            @empty
                            <p class="text-muted small text-center mb-0">Aucun sponsor enregistré.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
