{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Utilisateur — ' . $user->name)

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 20px; }
.cs-card__header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.cs-card__body { padding: 20px; }
.cs-tag { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
@media (max-width: 768px) { .cs-dash__main { margin-left: 0; } .cs-dash__content { padding: 16px; } }
</style>
@endpush

@section('content')
<div class="cs-dash">
    @include('admin.partials._sidebar')

    <main class="cs-dash__main">
        <div class="cs-dash__content">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}" class="text-decoration-none">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>

            {{-- Flash alerts --}}
            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            <div class="row g-4">
                {{-- Colonne gauche : infos principales --}}
                <div class="col-lg-8">
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width:48px;height:48px;background:#1A56A0;font-size:16px;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0" style="color:var(--text);">
                                        {{ $user->prenom ? $user->prenom . ' ' : '' }}{{ $user->name }}
                                    </h4>
                                    <span class="text-muted small">Inscrit le {{ $user->created_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ isset($user->actif) && !$user->actif ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2">
                                    {{ isset($user->actif) && !$user->actif ? 'Compte Bloqué' : 'Compte Actif' }}
                                </span>
                            </div>
                        </div>
                        <div class="cs-card__body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted small text-uppercase">Rôle actuel</dt>
                                <dd class="col-sm-8 fw-semibold">
                                    <span class="badge bg-primary rounded-pill px-3 py-1" style="background:#1A56A0 !important;">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4 text-muted small text-uppercase">Adresse Email</dt>
                                <dd class="col-sm-8">{{ $user->email }}</dd>

                                <dt class="col-sm-4 text-muted small text-uppercase">Téléphone</dt>
                                <dd class="col-sm-8">{{ $user->telephone ?? 'Non renseigné' }}</dd>

                                <dt class="col-sm-4 text-muted small text-uppercase">Dernière mise à jour</dt>
                                <dd class="col-sm-8">{{ $user->updated_at->diffForHumans() }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- Données du profil lié selon le rôle --}}
                    @if($user->isClub() && $user->club)
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-shield me-2 text-warning"></i>Profil Club Rattaché</h6>
                            <a href="{{ route('admin.clubs.show', $user->club) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                Voir la fiche club
                            </a>
                        </div>
                        <div class="cs-card__body">
                            <div class="row g-3">
                                <div class="col-sm-6"><strong>Nom du club :</strong> {{ $user->club->nom }}</div>
                                <div class="col-sm-6"><strong>Ville / Pays :</strong> {{ $user->club->ville }}, {{ $user->club->pays }}</div>
                                <div class="col-sm-6"><strong>Stade :</strong> {{ $user->club->stade ?? 'Non renseigné' }}</div>
                                <div class="col-sm-6"><strong>Statut :</strong> {{ $user->club->actif ? 'Actif' : 'Inactif' }}</div>
                            </div>
                        </div>
                    </div>
                    @elseif($user->isJoueur() && $user->joueur)
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-dribbble me-2 text-success"></i>Profil Joueur Rattaché</h6>
                            <a href="{{ route('admin.joueurs.show', $user->joueur) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                Voir la fiche joueur
                            </a>
                        </div>
                        <div class="cs-card__body">
                            <div class="row g-3">
                                <div class="col-sm-6"><strong>Poste :</strong> {{ $user->joueur->poste ?? 'Non spécifié' }}</div>
                                <div class="col-sm-6"><strong>Catégorie :</strong> {{ $user->joueur->categorie ?? 'Non spécifiée' }}</div>
                                <div class="col-sm-6"><strong>Club actuel :</strong> {{ $user->joueur->club?->nom ?? 'Sans club' }}</div>
                                <div class="col-sm-6"><strong>Visibilité recruteur :</strong> {{ $user->joueur->visible_recruteur ? 'Visible' : 'Masqué' }}</div>
                            </div>
                        </div>
                    </div>
                    @elseif($user->isAgent() && $user->agent)
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-briefcase me-2 text-purple"></i>Profil Agent Rattaché</h6>
                            <a href="{{ route('admin.agents.show', $user->agent) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Fiche Agent & Vérification
                            </a>
                        </div>
                        <div class="cs-card__body">
                            <div class="row g-3">
                                <div class="col-sm-6"><strong>Agence :</strong> {{ $user->agent->agence ?? 'Indépendant' }}</div>
                                <div class="col-sm-6"><strong>Accréditation :</strong> {{ $user->agent->numero_accreditation ?? 'Non renseignée' }}</div>
                                <div class="col-sm-6"><strong>Vérification :</strong> {{ $user->agent->verifie ? 'Vérifié' : 'En attente' }}</div>
                                <div class="col-sm-6"><strong>Joueurs gérés :</strong> {{ $user->agent->joueurs?->count() ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Colonne droite : Actions admin --}}
                <div class="col-lg-4">
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i>Actions d'administration</h6>
                        </div>
                        <div class="cs-card__body d-grid gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary rounded-3 text-white fw-semibold" style="background:#1A56A0;border-color:#1A56A0;">
                                <i class="bi bi-pencil me-1"></i> Modifier les informations
                            </a>

                            @if($user->id !== auth()->id())
                                @if(isset($user->actif) && !$user->actif)
                                    <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success w-100 rounded-3 fw-semibold">
                                            <i class="bi bi-unlock me-1"></i> Débloquer l'accès
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.block', $user) }}" onsubmit="return confirm('Bloquer cet utilisateur l\'empêchera de se connecter. Confirmer ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-warning w-100 rounded-3 fw-semibold">
                                            <i class="bi bi-lock me-1"></i> Bloquer l'accès
                                        </button>
                                    </form>
                                @endif

                                <hr class="my-2">

                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100 rounded-3 fw-semibold">
                                        <i class="bi bi-trash me-1"></i> Supprimer définitivement
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
