{{-- resources/views/joueurs/show.blade.php --}}
@extends('layouts.app')

@section('title', $joueur->prenom . ' ' . $joueur->nom)
@section('description', 'Profil de joueur – ' . ($joueur->poste ?? 'Sportif') . ' sur Connect Sport')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Fil d'Ariane --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('joueurs.sans-club') }}" class="text-decoration-none">Joueurs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $joueur->prenom }} {{ $joueur->nom }}</li>
                </ol>
            </nav>

            {{-- Profil principal --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div style="height: 6px; background: linear-gradient(90deg, #10B981, #34D399);"></div>

                <div class="card-body p-4 p-lg-5">
                    <div class="row g-5">
                        {{-- Colonne avatar + infos clés --}}
                        <div class="col-md-4 text-center text-md-start">
                            @if($joueur->avatar)
                                <img src="{{ Storage::url($joueur->avatar) }}" alt="Avatar" class="rounded-circle mb-3" width="160" height="160" style="object-fit: cover; border: 4px solid var(--bs-primary);">
                            @else
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mx-md-0 mb-3 fw-bold text-success" style="width: 160px; height: 160px; font-size: 4rem; border: 4px solid var(--bs-primary);">
                                    {{ strtoupper(substr($joueur->prenom ?? $joueur->name, 0, 1)) }}
                                </div>
                            @endif

                            <h2 class="fw-bold mb-1">{{ $joueur->prenom }} {{ $joueur->nom }}</h2>
                            <div class="text-primary fw-semibold mb-3">{{ $joueur->poste ?? 'Poste non renseigné' }}</div>

                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mb-4">
                                @if($joueur->pays)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill py-2 px-3">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $joueur->pays }}
                                </span>
                                @endif
                                @if($joueur->age)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill py-2 px-3">
                                    <i class="bi bi-cake2 me-1"></i> {{ $joueur->age }} ans
                                </span>
                                @endif
                                @if($joueur->taille)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill py-2 px-3">
                                    <i class="bi bi-rulers me-1"></i> {{ $joueur->taille }} cm
                                </span>
                                @endif
                                @if($joueur->poids)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill py-2 px-3">
                                    <i class="bi bi-activity me-1"></i> {{ $joueur->poids }} kg
                                </span>
                                @endif
                            </div>

                            {{-- Bouton de contact (pour clubs ou agents) --}}
                            @auth
                                @if(in_array(auth()->user()->role, ['club', 'agent']))
                                <a href="{{ route('messages.create', ['receiver_id' => $joueur->user_id]) }}" class="btn btn-warning rounded-pill w-100 w-md-auto px-4">
                                    <i class="bi bi-chat-dots me-2"></i>Contacter le joueur
                                </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill w-100 w-md-auto">
                                    Connectez-vous pour contacter
                                </a>
                            @endauth
                        </div>

                        {{-- Colonne détails --}}
                        <div class="col-md-8">
                            {{-- Biographie --}}
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="rounded bg-success" style="width: 24px; height: 3px;"></div>
                                    <h5 class="fw-bold mb-0">Biographie</h5>
                                </div>
                                <p class="text-body-secondary">{{ $joueur->biographie ?? 'Ce joueur n’a pas encore rempli sa biographie.' }}</p>
                            </div>

                            {{-- Statistiques (exemple) --}}
                            @if($joueur->statistiques)
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="rounded bg-success" style="width: 24px; height: 3px;"></div>
                                    <h5 class="fw-bold mb-0">Statistiques</h5>
                                </div>
                                <div class="row g-3">
                                    @foreach(json_decode($joueur->statistiques, true) ?? [] as $key => $value)
                                    <div class="col-6">
                                        <div class="bg-body-tertiary rounded-3 p-2 text-center">
                                            <div class="small text-body-secondary">{{ ucfirst($key) }}</div>
                                            <div class="fw-bold fs-5">{{ $value }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Expérience / clubs précédents --}}
                            @if($joueur->experiences)
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="rounded bg-success" style="width: 24px; height: 3px;"></div>
                                    <h5 class="fw-bold mb-0">Parcours</h5>
                                </div>
                                <p class="text-body-secondary">{{ $joueur->experiences }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection