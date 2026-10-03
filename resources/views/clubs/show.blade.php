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
                <a href="{{ route('clubs.joueurs', $club) }}"
                   class="badge bg-info bg-opacity-10 text-info rounded-pill text-decoration-none">
                    <i class="bi bi-people"></i> {{ $club->joueurs_count ?? 0 }} joueurs
                </a>
                <a href="{{ route('clubs.medias', $club) }}"
                   class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill text-decoration-none">
                    <i class="bi bi-images"></i> Médias publics
                </a>
                @if($club->evenements->isNotEmpty())
                <a href="{{ route('clubs.agenda', $club) }}"
                class="badge bg-success bg-opacity-10 text-success rounded-pill text-decoration-none">
                    <i class="bi bi-calendar-event"></i> {{ $club->evenements->count() }} événement(s) public(s)
                </a>
                @endif
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">
                    <i class="bi bi-heart-fill me-1"></i> {{ $club->supporters()->count() }} supporter(s)
                </span>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">À propos du club</h5>
                    <p class="text-body-secondary">{{ $club->description ?? 'Aucune description pour le moment.' }}</p>
                </div>
            </div>

            {{-- Sponsors actifs --}}
            @if($club->sponsors->count())
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">Sponsors & partenaires</h5>
                        <span class="text-body-secondary small">{{ $club->sponsors->count() }}</span>
                    </div>
                    <div class="row g-3">
                        @foreach($club->sponsors as $sponsor)
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 border rounded-3 p-3 h-100">
                                @if($sponsor->logo)
                                    <img src="{{ Storage::url($sponsor->logo) }}"
                                         width="48" height="48" class="rounded-2 object-fit-cover"
                                         alt="{{ $sponsor->nom }}">
                                @else
                                    <div class="rounded-2 bg-light d-flex align-items-center justify-content-center"
                                         style="width:48px;height:48px;">
                                        <i class="bi bi-building text-secondary"></i>
                                    </div>
                                @endif
                                <span class="fw-semibold text-truncate">{{ $sponsor->nom }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Agenda public --}}
            @if($club->evenements_count > 0)
                <a href="{{ route('clubs.agenda', $club) }}"
                class="badge bg-success bg-opacity-10 text-success rounded-pill text-decoration-none">
                    <i class="bi bi-calendar-event"></i> {{ $club->evenements_count }} événement(s) public(s)
                </a>
            @endif

            {{-- Section des opportunités du club --}}
            @if(!auth()->user()?->isSupporter() && $club->opportunites->count())
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
            @if(auth()->user()->isSupporter())
                @php
                    $supporter = auth()->user()->supporter;
                    $isFollowing = $supporter ? $supporter->isFollowing($club) : false;
                    $notifActive = $supporter ? $supporter->hasNotificationsActive($club) : false;
                @endphp
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-center">
                        <div class="mx-auto mb-3 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                            <i class="bi bi-heart-fill fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Espace Supporter</h5>

                        @if($isFollowing)
                            <div class="mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i> Vous soutenez ce club
                                </span>
                            </div>
                            <p class="small text-body-secondary mb-3">
                                Vous êtes abonné gratuitement aux actualités de {{ $club->nom }}.
                            </p>

                            <div class="d-flex flex-column gap-2">
                                {{-- Toggle notification --}}
                                <form method="POST" action="{{ route('supporter.club.notifications.toggle', $club) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $notifActive ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill w-100 py-2">
                                        <i class="bi {{ $notifActive ? 'bi-bell-fill text-success' : 'bi-bell-slash text-muted' }} me-1"></i>
                                        {{ $notifActive ? 'Notifications activées' : 'Notifications coupées' }}
                                    </button>
                                </form>

                                {{-- Se désabonner --}}
                                <form method="POST" action="{{ route('supporter.club.quitter', $club) }}" onsubmit="return confirm('Voulez-vous vraiment vous désabonner de {{ addslashes($club->nom) }} ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Se désabonner
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="small text-body-secondary mb-3">
                                Abonnez-vous gratuitement à {{ $club->nom }} pour recevoir ses prochains matchs, résultats et photos dans votre fil d'actualité.
                            </p>
                            <form method="POST" action="{{ route('supporter.club.suivre', $club) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-heart me-1"></i> Suivre ce club (gratuit)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @elseif(auth()->user()->role === 'joueur')
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-chat-dots fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">Contacter le club</h5>
                    <p class="small text-body-secondary">Envoyez un message au responsable du club.</p>
                    <a href="{{ route('messages.conversation', $club->user_id) }}" class="btn btn-primary rounded-pill w-100">Envoyer un message</a>
                </div>
            </div>
            @endif
            @else
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-heart fs-1 text-danger"></i>
                    <h5 class="fw-bold mt-2">Supporter du club ?</h5>
                    <p class="small text-body-secondary">Connectez-vous ou inscrivez-vous gratuitement pour suivre les matchs et résultats de {{ $club->nom }}.</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">Connexion</a>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>
@endsection