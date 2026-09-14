@extends('layouts.app')

@section('title', $equipe->nom . ' — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('club.equipes.index') }}" class="btn btn-outline-secondary rounded-circle p-2" title="Retour">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h2 fw-bold mb-0">{{ $equipe->nom }}</h1>
            </div>
            <p class="text-muted small mt-1">{{ ucfirst($equipe->genre) }} · {{ ucfirst($equipe->categorie) }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('club.equipes.edit', $equipe) }}" class="btn btn-outline-warning rounded-pill">
                <i class="bi bi-pencil"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Colonne gauche : description et infos --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-warning me-2"></i>Description</h5>
                    <p class="text-muted">{{ $equipe->description ?: 'Aucune description fournie.' }}</p>
                    <hr>
                    <div class="d-flex justify-content-between small">
                        <span class="fw-semibold">Effectif :</span>
                        <span>{{ $joueurs->count() }} joueurs</span>
                    </div>
                    <div class="d-flex justify-content-between small mt-2">
                        <span class="fw-semibold">Statut :</span>
                        <span class="badge {{ $equipe->actif ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                            {{ $equipe->actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Lien vers autre module (ex: matchs à venir) à ajouter plus tard --}}
        </div>

        {{-- Colonne droite : liste des joueurs --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-warning me-2"></i>Joueurs de l'équipe</h5>
                </div>
                <div class="card-body p-0">
                    @if($joueurs->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-person-x fs-1 text-muted"></i>
                            <p class="mt-2">Aucun joueur dans cette équipe.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Joueur</th>
                                        <th>Poste</th>
                                        <th>Catégorie</th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($joueurs as $joueur)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-primary" style="width:36px;height:36px;">
                                                    {{ substr($joueur->user->prenom ?? '?', 0, 1) }}{{ substr($joueur->user->name ?? '', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $joueur->nomComplet() }}</div>
                                                    <div class="small text-muted">{{ $joueur->age() }} ans</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $joueur->poste ?: '—' }}</td>
                                        <td>{{ $joueur->categorie ?: '—' }}</td>
                                        <td>
                                            @if($joueur->actif)
                                                <span class="badge bg-success rounded-pill">Actif</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('club.joueurs.show', $joueur) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Voir le profil">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection