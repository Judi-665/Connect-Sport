{{-- resources/views/clubs/show.blade.php --}}
@extends('layouts.app')

@section('title', $club->nom)
@section('description', $club->description ?? 'Club sportif sur Connect Sport')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        {{-- Colonne principale --}}
        <div class="col-lg-8">
            {{-- Bannière et logo --}}
            <div class="position-relative rounded-4 overflow-hidden mb-4" style="height: 200px; background: linear-gradient(135deg, #0D2E5C, #1A56A0);">
                @if($club->banniere)
                    <img src="{{ Storage::url($club->banniere) }}" class="w-100 h-100 object-fit-cover" alt="Bannière">
                @endif
                <div class="position-absolute bottom-0 start-0 p-3">
                    @if($club->logo)
                        <img src="{{ Storage::url($club->logo) }}" class="rounded-3 border border-2 border-white shadow" width="80" height="80" style="object-fit: cover;">
                    @else
                        <div class="rounded-3 bg-white bg-opacity-25 d-flex align-items-center justify-content-center fw-bold text-white" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($club->nom,0,2)) }}
                        </div>
                    @endif
                </div>
            </div>

            <h1 class="fw-bold mb-2">{{ $club->nom }}</h1>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $club->sport->nom ?? 'Sport' }}</span>
                @if($club->ville)
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill"><i class="bi bi-geo-alt"></i> {{ $club->ville }}</span>
                @endif
                @if($club->pays)
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill"><i class="bi bi-flag"></i> {{ $club->pays }}</span>
                @endif
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill"><i class="bi bi-people"></i> {{ $club->joueurs_count ?? 0 }} joueurs</span>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">À propos du club</h5>
                    <p class="text-body-secondary">{{ $club->description ?? 'Aucune description pour le moment.' }}</p>
                </div>
            </div>

            {{-- Section des opportunités du club --}}
            @if($club->opportunites->count())
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Opportunités publiées</h5>
                    <div class="list-group list-group-flush">
                        @foreach($club->opportunites as $opp)
                        <a href="{{ route('opportunites.show', $opp->id) }}" class="list-group-item list-group-item-action bg-transparent px-0 py-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="fw-semibold">{{ $opp->titre }}</h6>
                                    <small class="text-body-secondary">{{ $opp->poste }} • {{ $opp->date->format('d/m/Y') }}</small>
                                </div>
                                <i class="bi bi-chevron-right text-primary"></i>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Colonne latérale --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Informations</h5>
                    <ul class="list-unstyled">
                        @if($club->email)<li class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> {{ $club->email }}</li>@endif
                        @if($club->telephone)<li class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> {{ $club->telephone }}</li>@endif
                        @if($club->site_web)<li class="mb-2"><i class="bi bi-globe me-2 text-primary"></i> <a href="{{ $club->site_web }}" target="_blank">{{ $club->site_web }}</a></li>@endif
                        @if($club->adresse)<li class="mb-2"><i class="bi bi-building me-2 text-primary"></i> {{ $club->adresse }}</li>@endif
                    </ul>
                </div>
            </div>

            @auth
            @if(auth()->user()->role === 'joueur')
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-chat-dots fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">Contacter le club</h5>
                    <p class="small text-body-secondary">Envoyez un message au responsable du club.</p>
                    <a href="#" class="btn btn-primary rounded-pill w-100">Envoyer un message</a>
                </div>
            </div>
            @endif
            @else
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-lock fs-1 text-body-tertiary"></i>
                    <h5 class="fw-bold mt-2">Connectez-vous</h5>
                    <p class="small text-body-secondary">Pour contacter ce club, créez un compte joueur.</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill">Connexion</a>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection