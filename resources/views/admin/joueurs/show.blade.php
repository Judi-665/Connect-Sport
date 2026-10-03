{{-- resources/views/admin/joueurs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails Joueur — ' . $joueur->nomComplet())

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
                    <li class="breadcrumb-item"><a href="{{ route('admin.joueurs') }}" class="text-decoration-none">Joueurs</a></li>
                    <li class="breadcrumb-item active">{{ $joueur->nomComplet() }}</li>
                </ol>
            </nav>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Fiche Joueur En-tête --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:52px;height:52px;background:#10b981;font-size:18px;">
                            {{ strtoupper(substr($joueur->nomComplet(), 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0" style="color:var(--text);">{{ $joueur->nomComplet() }}</h3>
                            <span class="text-muted small">
                                Poste : <strong>{{ $joueur->poste ?? '-' }}</strong> · Catégorie : <strong>{{ $joueur->categorie ?? '-' }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        {{-- Visibilité Recruteur --}}
                        <form method="POST" action="{{ route('admin.joueurs.visibilite', $joueur) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-{{ $joueur->visible_recruteur ? 'secondary' : 'info' }} rounded-pill px-3">
                                <i class="bi {{ $joueur->visible_recruteur ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i>
                                {{ $joueur->visible_recruteur ? 'Retirer visibilité recruteur' : 'Rendre visible' }}
                            </button>
                        </form>

                        {{-- Modération Actif / Désactivé --}}
                        <form method="POST" action="{{ route('admin.joueurs.toggle', $joueur) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-{{ $joueur->actif ? 'danger' : 'success' }} rounded-pill px-3">
                                <i class="bi {{ $joueur->actif ? 'bi-slash-circle' : 'bi-check-circle' }} me-1"></i>
                                {{ $joueur->actif ? 'Désactiver le profil' : 'Réactiver le profil' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="cs-card__body">
                    <div class="row g-4">
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Club Actuel</span>
                            <strong>{{ $joueur->club?->nom ?? 'Sans Club (Libre)' }}</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Contact & Email</span>
                            <strong>{{ $joueur->user->email ?? '-' }}</strong><br>
                            <span class="small text-muted">{{ $joueur->telephone ?? $joueur->user->telephone ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Naissance / Nationalité</span>
                            <strong>{{ $joueur->date_naissance ? $joueur->date_naissance->format('d/m/Y') : '-' }}</strong> · {{ $joueur->nationalite ?? 'Béninoise' }}
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Statut Modération</span>
                            <span class="badge {{ $joueur->actif ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                {{ $joueur->actif ? 'Profil Conforme' : 'Signalé / Désactivé' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Licences, Statistiques et Carrière --}}
            <div class="row g-4">
                <div class="col-lg-6">
                    {{-- Licences du joueur --}}
                    <div class="cs-card mb-4">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-check me-2 text-success"></i>Licences Enregistrées ({{ $joueur->licences->count() }})</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            @forelse($joueur->licences as $lic)
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold small" style="color:var(--text);">N° {{ $lic->numero_licence }} (Club : {{ $lic->club?->nom ?? '-' }})</div>
                                    <div class="text-muted small">Expire le : {{ $lic->date_expiration ? $lic->date_expiration->format('d/m/Y') : '-' }}</div>
                                </div>
                                <span class="badge {{ $lic->estExpiree() ? 'bg-danger' : 'bg-success' }} rounded-pill">
                                    {{ $lic->estExpiree() ? 'Expirée' : 'Valide' }}
                                </span>
                            </div>
                            @empty
                            <div class="p-3 text-center text-muted small">Aucune licence enregistrée pour ce joueur.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Statistiques --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Statistiques Sportives</h6>
                        </div>
                        <div class="cs-card__body">
                            @forelse($joueur->statistiques as $stat)
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom small">
                                <span><strong>Saison {{ $stat->saison }}</strong> ({{ $stat->matchs_joues ?? 0 }} matchs)</span>
                                <span class="badge bg-light text-dark border">{{ $stat->buts ?? 0 }} Buts · {{ $stat->passes_decisives ?? 0 }} Passes</span>
                            </div>
                            @empty
                            <p class="text-muted small text-center mb-0">Aucune statistique saisie.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- Historique Carrière --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-warning"></i>Parcours & Carrière</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            @forelse($joueur->carrieres as $carriere)
                            <div class="p-3 border-bottom">
                                <div class="fw-semibold small" style="color:var(--text);">{{ $carriere->club_nom ?? 'Club' }} ({{ $carriere->saison ?? '-' }})</div>
                                <div class="text-muted small">{{ $carriere->poste ?? '-' }} · {{ $carriere->description ?? '' }}</div>
                            </div>
                            @empty
                            <div class="p-3 text-center text-muted small">Aucune étape de carrière documentée.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
