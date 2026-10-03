{{-- resources/views/opportunites/show.blade.php --}}
@extends('layouts.app')

@section('title', $opportunite->titre)
@section('description', $opportunite->description ?? 'Opportunité sportive sur Connect Sport')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Fil d'Ariane simple --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('opportunites.index') }}" class="text-decoration-none">Opportunités</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $opportunite->titre }}</li>
                </ol>
            </nav>

            {{-- Carte principale --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                {{-- Bandeau supérieur décoratif --}}
                <div style="height: 6px; background: linear-gradient(90deg, #F97316, #FFD166);"></div>

                <div class="card-body p-4 p-lg-5">
                    {{-- En-tête avec type --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <h1 class="fw-bold mb-0">{{ $opportunite->titre }}</h1>
                        <span class="badge {{ $opportunite->type === 'transfert' ? 'bg-danger' : ($opportunite->type === 'recrutement' ? 'bg-success' : 'bg-info') }} rounded-pill px-3 py-2">
                            {{ ucfirst($opportunite->type) }}
                        </span>
                    </div>

                    {{-- Métadonnées en lignes --}}
                    <div class="d-flex flex-wrap gap-4 mb-4 pb-3 border-bottom">
                        @if($opportunite->club)
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-trophy-fill text-primary"></i>
                            <span><strong>Club :</strong> {{ $opportunite->club->nom }}</span>
                        </div>
                        @endif
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-briefcase-fill text-primary"></i>
                            <span><strong>Poste :</strong> {{ $opportunite->poste ?? 'Non spécifié' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <span><strong>Pays :</strong> {{ $opportunite->pays ?? 'Afrique' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-event-fill text-primary"></i>
                            <span><strong>Date limite :</strong> {{ \Carbon\Carbon::parse($opportunite->date)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    {{-- Description détaillée --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded bg-primary" style="width: 24px; height: 3px;"></div>
                            <h5 class="fw-bold mb-0">Description du poste</h5>
                        </div>
                        <div class="text-body-secondary" style="line-height: 1.7;">
                            {!! nl2br(e($opportunite->description ?? 'Aucune description fournie.')) !!}
                        </div>
                    </div>

                    {{-- Compétences requises --}}
                    @if($opportunite->competences)
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded bg-primary" style="width: 24px; height: 3px;"></div>
                            <h5 class="fw-bold mb-0">Compétences recherchées</h5>
                        </div>
                        <div class="text-body-secondary">
                            {!! nl2br(e($opportunite->competences)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Bouton d'action conditionnel --}}
                    <div class="mt-4 pt-3 border-top">
                        @auth
                            @if(in_array(auth()->user()->role, ['joueur', 'agent']))
                                <div class="alert alert-primary d-flex align-items-center gap-2 mb-3">
                                    <i class="bi bi-envelope-paper-fill fs-5"></i>
                                    <span>Vous êtes intéressé(e) ? Postulez directement via la messagerie.</span>
                                </div>
                                <a href="{{ route('messages.conversation', ['user' => $opportunite->club->user_id]) }}" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold">
                                    <i class="bi bi-send me-2"></i>Postuler maintenant
                                </a>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Seuls les joueurs et les agents peuvent postuler à cette opportunité.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center">
                                <span><i class="bi bi-lock me-2"></i>Vous devez être connecté pour postuler.</span>
                                <div>
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary rounded-pill me-2">Connexion</a>
                                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary rounded-pill">Inscription</a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Carte informations complémentaires (club émetteur) --}}
            @if($opportunite->club)
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        @if($opportunite->club->logo)
                            <img src="{{ Storage::url($opportunite->club->logo) }}" alt="{{ $opportunite->club->nom }}" width="56" height="56" class="rounded-3 object-fit-cover">
                        @else
                            <div class="rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 56px; height: 56px; font-size: 1.4rem;">
                                {{ strtoupper(substr($opportunite->club->nom, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h5 class="fw-bold mb-0">{{ $opportunite->club->nom }}</h5>
                            <div class="text-body-secondary small">{{ $opportunite->club->ville ?? '' }} {{ $opportunite->club->pays ?? '' }}</div>
                            <a href="{{ route('clubs.show', $opportunite->club->slug) }}" class="btn btn-link text-primary p-0 mt-1">Voir le profil du club →</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection