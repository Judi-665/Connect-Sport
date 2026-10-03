{{-- resources/views/parent/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Espace Parent — Connect Sport')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- ═══════════ HERO BANNER PARENT ═══════════ --}}
    <div class="rounded-4 p-4 p-md-5 mb-4 shadow-sm position-relative overflow-hidden text-white"
         style="background: linear-gradient(135deg, #0D2E5C 0%, #1A56A0 55%, #0B1E38 100%);">

        {{-- Glows décoratifs --}}
        <div class="position-absolute rounded-circle" style="top:-60px;right:-60px;width:240px;height:240px;
             background: radial-gradient(circle, rgba(45,212,191,.25) 0%, rgba(45,212,191,0) 70%); pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" style="bottom:-40px;left:25%;width:180px;height:180px;
             background: radial-gradient(circle, rgba(255,255,255,.05) 0%, transparent 70%); pointer-events:none;"></div>

        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge fw-bold text-uppercase px-2 py-1 rounded-pill"
                          style="background:#2DD4BF;color:#064E3B;font-size:.72rem;letter-spacing:1px;">
                        <i class="bi bi-people-fill me-1"></i> Espace Parent
                    </span>
                    @if(auth()->user()->telephone)
                        <span class="text-white-50 small">
                            <i class="bi bi-telephone"></i> {{ auth()->user()->telephone }}
                        </span>
                    @endif
                </div>

                @php
                    $heure = (int) date('H');
                    $salutation = match(true) {
                        $heure >= 5 && $heure < 12 => 'Bonjour',
                        $heure >= 12 && $heure < 18 => 'Bon après-midi',
                        default => 'Bonsoir'
                    };
                @endphp

                <h1 class="display-6 fw-bold mb-2 text-white">
                    {{ $salutation }}, {{ auth()->user()->prenom ?? auth()->user()->name }} !
                </h1>
                <p class="lead text-white-50 fs-6 mb-4">
                    Suivez toute l'actualité de vos enfants : calendrier des matchs, statistiques validées, licences et échanges directs avec leurs clubs.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('parent.add-joueur') }}" class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-person-plus-fill me-1"></i> Lier un enfant
                    </a>
                    @if($clubsEnfants->count() === 1)
                        <a href="{{ route('clubs.show', $clubsEnfants->first()) }}" class="btn btn-outline-light rounded-pill px-4">
                            <i class="bi bi-shield me-1"></i> Voir le club de mon enfant
                        </a>
                    @elseif($clubsEnfants->isNotEmpty())
                        <div class="dropdown">
                            <button class="btn btn-outline-light rounded-pill px-4 dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-shield me-1"></i> Clubs de mes enfants
                            </button>
                            <ul class="dropdown-menu shadow">
                                @foreach($clubsEnfants as $clubEnfant)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('clubs.show', $clubEnfant) }}">
                                            <i class="bi bi-shield me-2 text-primary"></i>{{ $clubEnfant->nom }}
                                            @if($clubEnfant->ville)
                                                <span class="d-block small text-body-secondary ms-4">{{ $clubEnfant->ville }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('clubs.index') }}">Explorer tous les clubs</a></li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('clubs.index') }}" class="btn btn-outline-light rounded-pill px-4">
                            <i class="bi bi-shield me-1"></i> Découvrir les clubs
                        </a>
                    @endif
                    
                </div>
            </div>

            {{-- Cartes KPI --}}
            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach([
                        ['enfants', 'Enfants liés', 'bi-people-fill', 'text-warning', 'Profils rattachés'],
                        ['avec_club', 'En club', 'bi-shield-fill', 'text-info', 'Clubs formateurs'],
                        ['licences_actives', 'Licences actives', 'bi-patch-check-fill', 'text-success', 'Validées & conformes'],
                    ] as [$key, $label, $icon, $iconColor, $sub])
                    <div class="col-6">
                        <div class="rounded-3 border border-white border-opacity-25 bg-white bg-opacity-10 h-100 p-3"
                             style="backdrop-filter: blur(8px);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-white-50 small">{{ $label }}</span>
                                <i class="bi {{ $icon }} {{ $iconColor }} fs-5"></i>
                            </div>
                            <div class="fs-3 fw-bold text-white">{{ $stats[$key] ?? 0 }}</div>
                            <div class="text-white-50" style="font-size:.75rem;">{{ $sub }}</div>
                        </div>
                    </div>
                    @endforeach

                    {{-- 4ème KPI : Messagerie --}}
                    <div class="col-6">
                        <div class="rounded-3 border border-white border-opacity-25 bg-white bg-opacity-10 h-100 p-3"
                             style="backdrop-filter: blur(8px);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-white-50 small">Messages</span>
                                <i class="bi bi-chat-dots-fill text-danger fs-5"></i>
                            </div>
                            <div class="fs-3 fw-bold text-white">
                                <a href="{{ route('messages.inbox') }}" class="text-white text-decoration-none">
                                    <i class="bi bi-envelope-open small"></i> Club
                                </a>
                            </div>
                            <div class="text-white-50" style="font-size:.75rem;">Contact direct</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ ONGLETS STYLE BOOTSTRAP ═══════════ --}}
    <ul class="nav nav-tabs mb-4" id="parentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="enfants-tab" data-bs-toggle="tab" data-bs-target="#enfants-pane" type="button" role="tab" aria-selected="true">
                <i class="bi bi-people-fill me-2"></i>Mes enfants ({{ $liens->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <a href="{{ route('parent.add-joueur') }}" class="nav-link text-decoration-none">
                <i class="bi bi-person-plus me-2"></i>Lier un nouvel enfant
            </a>
        </li>
        
        
    </ul>

    <div class="tab-content" id="parentTabsContent">

        {{-- ═══════════ TAB 1 : MES ENFANTS ═══════════ --}}
        <div class="tab-pane fade show active" id="enfants-pane" role="tabpanel" tabindex="0">

            @if($liens->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 text-center p-5 my-4">
                    <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:80px;height:80px;background:rgba(13,148,136,.12);color:#0D9488;">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Bienvenue sur votre Espace Parent !</h3>
                    <p class="text-body-secondary mx-auto mb-4" style="max-width:560px;">
                        Vous n'avez pas encore lié d'enfant à votre compte. Recherchez le profil de votre enfant pour suivre ses matchs, ses statistiques validées, son agenda et communiquer facilement avec son club.
                    </p>
                    <div>
                        <a href="{{ route('parent.add-joueur') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" style="background:#0D9488;border-color:#0D9488;">
                            <i class="bi bi-person-plus me-2"></i>Lier mon premier enfant
                        </a>
                    </div>
                </div>
            @else

                <div class="row g-4 mb-5">
                    @foreach($liens as $lien)
                    @php
                        $joueur       = $lien->joueur;
                        if (!$joueur) continue;
                        $prenom       = $joueur->user?->prenom ?? $joueur->user?->name ?? '—';
                        $nom          = $joueur->user?->name ?? '';
                        $initiales    = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
                        $licenceOk    = $joueur->licenceActive !== null;
                        $lienLabel    = match($lien->lien) {
                            'pere'   => 'Père',
                            'mere'   => 'Mère',
                            'tuteur' => 'Tuteur légal',
                            default  => 'Parent'
                        };
                    @endphp

                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative">
                            {{-- Barre décorative haute --}}
                            <div style="height:5px;background:linear-gradient(90deg, #0D9488, #1A56A0);"></div>

                            <div class="card-body p-4 d-flex flex-column">

                                {{-- Header joueur --}}
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                                         style="width:54px;height:54px;background:linear-gradient(135deg,#0D2E5C,#0D9488);color:#fff;font-size:18px;">
                                        @if($joueur->user?->avatar)
                                            <img src="{{ Storage::url($joueur->user->avatar) }}" alt="{{ $prenom }}" class="w-100 h-100 rounded-circle object-fit-cover">
                                        @else
                                            {{ $initiales }}
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <h5 class="fw-bold mb-1 text-truncate">{{ $prenom }} {{ $nom }}</h5>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <span class="badge rounded-pill bg-teal text-white" style="background:#0D9488;font-size:11px;">
                                                <i class="bi bi-heart-fill me-1"></i>{{ $lienLabel }}
                                            </span>
                                            @if($joueur->poste)
                                                <span class="badge rounded-pill bg-body-secondary text-body" style="font-size:11px;">
                                                    {{ ucfirst($joueur->poste) }}
                                                </span>
                                            @endif
                                            @if($joueur->categorie)
                                                <span class="badge rounded-pill bg-body-secondary text-body" style="font-size:11px;">
                                                    {{ ucfirst($joueur->categorie) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Infos club & statut --}}
                                <div class="p-3 rounded-3 bg-body-tertiary mb-3 small d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-body-secondary"><i class="bi bi-shield me-2 text-primary"></i>Club :</span>
                                        @if($joueur->club)
                                            <span class="fw-semibold text-truncate ms-2">{{ $joueur->club->nom }}</span>
                                        @else
                                            <span class="text-muted fst-italic">Sans club</span>
                                        @endif
                                    </div>

                                    @if($joueur->equipe)
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-body-secondary"><i class="bi bi-people me-2 text-info"></i>Équipe :</span>
                                        <span class="fw-semibold">{{ $joueur->equipe->nom }}</span>
                                    </div>
                                    @endif

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-body-secondary"><i class="bi bi-patch-check me-2 text-warning"></i>Licence :</span>
                                        @if($licenceOk)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                <i class="bi bi-check-circle-fill me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">
                                                <i class="bi bi-exclamation-circle me-1"></i>Non renseignée
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-auto pt-3 border-top d-flex flex-column gap-2">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('parent.joueur.show', $joueur) }}" class="btn btn-primary rounded-pill flex-grow-1 fw-semibold py-2" style="background:#0D9488;border-color:#0D9488;">
                                            <i class="bi bi-person-lines-fill me-1"></i> Profil complet
                                        </a>
                                        <a href="{{ route('parent.joueur.stats', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3" title="Statistiques">
                                            <i class="bi bi-bar-chart-fill"></i>
                                        </a>
                                        <a href="{{ route('parent.joueur.agenda', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3" title="Agenda & Matchs">
                                            <i class="bi bi-calendar3"></i>
                                        </a>
                                    </div>

                                    <div class="d-flex gap-2">
                                        @if($joueur->club)
                                        <a href="{{ route('parent.joueur.contacter-club', $joueur) }}" class="btn btn-outline-primary rounded-pill flex-grow-1 small py-1">
                                            <i class="bi bi-chat-dots me-1"></i> Contacter le club
                                        </a>
                                        @endif
                                        <a href="{{ route('parent.joueur.signalement', $joueur) }}" class="btn btn-outline-warning rounded-pill small py-1" title="Signaler une difficulté / Souhait">
                                            <i class="bi bi-exclamation-triangle"></i>
                                        </a>
                                        <a href="{{ route('parent.joueur.transferts', $joueur) }}" class="btn btn-outline-secondary rounded-pill small py-1" title="Transferts">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </a>
                                        <form method="POST" action="{{ route('parent.lien.remove', $lien) }}" onsubmit="return confirm('Retirer le lien avec cet enfant ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger rounded-pill small py-1 px-2" title="Retirer ce lien">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- ═══════════ ACCÈS RAPIDES ═══════════ --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill text-warning"></i> Accès rapides
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('parent.add-joueur') }}" class="card bg-body-tertiary border-0 rounded-3 p-3 text-decoration-none text-body h-100 hover-shadow">
                                <div class="fw-bold mb-1"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Lier un autre enfant</div>
                                <div class="text-body-secondary small">Ajoutez un autre profil de joueur à suivre.</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('clubs.index') }}" class="card bg-body-tertiary border-0 rounded-3 p-3 text-decoration-none text-body h-100 hover-shadow">
                                <div class="fw-bold mb-1"><i class="bi bi-shield-shaded me-2 text-success"></i>Consulter les clubs</div>
                                <div class="text-body-secondary small">Découvrez les structures et académies de football.</div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('joueurs.sans-club') }}" class="card bg-body-tertiary border-0 rounded-3 p-3 text-decoration-none text-body h-100 hover-shadow">
                                <div class="fw-bold mb-1"><i class="bi bi-people me-2 text-info"></i>Annuaire des joueurs</div>
                                <div class="text-body-secondary small">Parcourez les talents répertoriés sur la plateforme.</div>
                            </a>
                        </div>
                    </div>
                </div>

            @endif

        </div>

    </div>

</div>
@endsection
