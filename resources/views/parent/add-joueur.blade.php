{{-- resources/views/parent/add-joueur.blade.php --}}
@extends('layouts.app')

@section('title', 'Lier un enfant — Espace Parent')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Espace Parent</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lier un enfant</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Carte En-tête de recherche --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div style="height:5px;background:linear-gradient(90deg, #0D9488, #1A56A0);"></div>
                <div class="card-body p-4 p-md-5">

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:56px;height:56px;background:rgba(13,148,136,.12);color:#0D9488;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold mb-1">Rechercher et lier un joueur</h2>
                            <p class="text-body-secondary small mb-0">
                                Recherchez le profil de votre enfant par son nom, prénom, club ou téléphone, puis sélectionnez votre lien de parenté pour l'ajouter à votre espace.
                            </p>
                        </div>
                    </div>

                    {{-- Formulaire de recherche --}}
                    <form method="GET" action="{{ route('parent.add-joueur') }}" class="mb-2">
                        <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border">
                            <span class="input-group-text bg-body border-0 ps-4 text-primary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   class="form-control border-0 px-2"
                                   placeholder="Nom, prénom, club, email ou poste du joueur..."
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background:#0D9488;border-color:#0D9488;">
                                Rechercher
                            </button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center justify-content-between mt-3 text-body-secondary small flex-wrap gap-2">
                        <span><i class="bi bi-info-circle me-1"></i>Le joueur doit avoir un compte <strong>Joueur</strong> sur Connect Sport.</span>
                        @if(request('search'))
                            <a href="{{ route('parent.add-joueur') }}" class="text-decoration-none text-danger">
                                <i class="bi bi-x-circle me-1"></i>Réinitialiser la recherche
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Liste des Résultats --}}
            @if($resultats->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <div class="mx-auto mb-3 rounded-circle bg-body-tertiary d-flex align-items-center justify-content-center text-body-tertiary" style="width:70px;height:70px;">
                        <i class="bi bi-person-x fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Aucun joueur trouvé</h5>
                    <p class="text-body-secondary small mb-3">
                        @if(request('search'))
                            Aucun profil ne correspond à « {{ request('search') }} ». Vérifiez l'orthographe ou essayez un mot-clé différent.
                        @else
                            Aucun joueur disponible pour le moment.
                        @endif
                    </p>
                    <div>
                        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Retour au tableau de bord
                        </a>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-body py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-uppercase" style="letter-spacing:1px;color:#0D9488;">
                                <i class="bi bi-person-check-fill me-1"></i>
                                @if(request('search'))
                                    {{ $resultats->count() }} résultat(s) pour « {{ request('search') }} »
                                @else
                                    Joueurs récents suggérés ({{ $resultats->count() }})
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        @foreach($resultats as $joueur)
                        @php
                            $prenom    = $joueur->user?->prenom ?? $joueur->user?->name ?? '—';
                            $nom       = $joueur->user?->name ?? '';
                            $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
                        @endphp
                        <div class="list-group-item p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                                {{-- Identité Joueur --}}
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0"
                                         style="width:52px;height:52px;background:linear-gradient(135deg,#0D2E5C,#0D9488);font-size:16px;">
                                        @if($joueur->user?->avatar)
                                            <img src="{{ Storage::url($joueur->user->avatar) }}" alt="{{ $prenom }}" class="w-100 h-100 rounded-circle object-fit-cover">
                                        @else
                                            {{ $initiales }}
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $prenom }} {{ $nom }}</h6>
                                        <div class="d-flex gap-1 flex-wrap small">
                                            @if($joueur->club)
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                                    <i class="bi bi-shield me-1"></i>{{ $joueur->club->nom }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill">
                                                    Sans club
                                                </span>
                                            @endif
                                            @if($joueur->poste)
                                                <span class="badge bg-body-secondary text-body rounded-pill">
                                                    {{ ucfirst($joueur->poste) }}
                                                </span>
                                            @endif
                                            @if($joueur->categorie)
                                                <span class="badge bg-body-secondary text-body rounded-pill">
                                                    {{ ucfirst($joueur->categorie) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Formulaire pour lier --}}
                                <form method="POST" action="{{ route('parent.confirm-joueur') }}" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="joueur_id" value="{{ $joueur->id }}">
                                    <select name="lien" class="form-select form-select-sm rounded-pill" style="min-width:140px;" required>
                                        <option value="">Lien de parenté...</option>
                                        <option value="pere">Père</option>
                                        <option value="mere">Mère</option>
                                        <option value="tuteur">Tuteur légal</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-sm" style="background:#0D9488;border-color:#0D9488;">
                                        <i class="bi bi-link-45deg me-1"></i> Lier
                                    </button>
                                </form>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Bouton retour --}}
            <div class="text-center mt-3">
                <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Retour au tableau de bord
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
