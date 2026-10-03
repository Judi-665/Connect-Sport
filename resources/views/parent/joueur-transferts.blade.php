{{-- resources/views/parent/joueur-transferts.blade.php --}}
@extends('layouts.app')

@section('title', 'Transferts de ' . ($joueur->user->prenom ?? $joueur->user->name) . ' — Espace Parent')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Espace Parent</a></li>
            <li class="breadcrumb-item"><a href="{{ route('parent.joueur.show', $joueur) }}" class="text-decoration-none">{{ $joueur->user->prenom ?? $joueur->user->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Transferts</li>
        </ol>
    </nav>

    @php $prenom = $joueur->user->prenom ?? $joueur->user->name; @endphp

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                     style="width:54px;height:54px;background:rgba(124,58,237,.1);color:#7C3AED;font-size:24px;">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-1">Transferts de {{ $prenom }}</h1>
                    <div class="text-body-secondary small">
                        <i class="bi bi-info-circle me-1"></i>Vue en lecture seule — géré par les clubs et agents accrédités
                    </div>
                </div>
            </div>
            <a href="{{ route('parent.joueur.show', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Retour au profil
            </a>
        </div>
    </div>

    @if($transferts->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <div class="mx-auto mb-3 rounded-circle bg-body-tertiary d-flex align-items-center justify-content-center text-muted" style="width:70px;height:70px;">
                <i class="bi bi-arrow-left-right fs-1"></i>
            </div>
            <h5 class="fw-bold mb-1">Aucun mouvement pour le moment</h5>
            <p class="text-body-secondary small mb-3">Aucun transfert ou négociation n'a été enregistré pour {{ $prenom }}.</p>
            <div>
                <a href="{{ route('parent.joueur.show', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Retour au profil
                </a>
            </div>
        </div>
    @else

        @php
            $enCours = $transferts->filter(fn($t) => $t->estEnCours());
            $historique = $transferts->filter(fn($t) => !$t->estEnCours());
        @endphp

        {{-- En cours --}}
        @if($enCours->count() > 0)
        <div class="mb-5">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-hourglass-split text-warning"></i> En cours ({{ $enCours->count() }})
            </h5>

            <div class="row g-3">
                @foreach($enCours as $t)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1">
                                En négociation
                            </span>
                            <span class="badge bg-body-secondary text-body rounded-pill">
                                {{ ucfirst($t->type ?? 'Transfert') }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-body-tertiary mb-3 text-center">
                            <div class="text-truncate px-2" style="flex:1;">
                                <div class="fw-bold small text-truncate">{{ $t->clubSource?->nom ?? 'Club actuel' }}</div>
                                <div class="text-body-secondary" style="font-size:11px;">Club source</div>
                            </div>
                            <div class="px-2 text-primary fs-5">
                                <i class="bi bi-arrow-right"></i>
                            </div>
                            <div class="text-truncate px-2" style="flex:1;">
                                <div class="fw-bold small text-truncate">{{ $t->clubDestinataire?->nom ?? '—' }}</div>
                                <div class="text-body-secondary" style="font-size:11px;">Club récepteur</div>
                            </div>
                        </div>

                        <div class="small d-flex flex-column gap-1 text-body-secondary">
                            @if(!$t->montant_confidentiel && $t->montant)
                                <div class="d-flex justify-content-between">
                                    <span>Montant estimé :</span>
                                    <strong class="text-body">{{ $t->montantAffiche() }}</strong>
                                </div>
                            @endif
                            @if($t->date_effet)
                                <div class="d-flex justify-content-between">
                                    <span>Date d'effet :</span>
                                    <strong class="text-body">{{ \Carbon\Carbon::parse($t->date_effet)->format('d/m/Y') }}</strong>
                                </div>
                            @endif
                            @if($t->agent)
                                <div class="d-flex justify-content-between">
                                    <span>Agent mandaté :</span>
                                    <strong class="text-body">{{ $t->agent->user?->prenom }} {{ $t->agent->user?->name }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Historique --}}
        @if($historique->count() > 0)
        <div>
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-muted"></i> Historique des transferts
            </h5>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="list-group list-group-flush">
                    @foreach($historique as $t)
                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="fw-semibold">{{ $t->clubSource?->nom ?? '—' }}</span>
                            <i class="bi bi-arrow-right mx-2 text-muted"></i>
                            <span class="fw-semibold">{{ $t->clubDestinataire?->nom ?? '—' }}</span>
                            <span class="badge bg-body-secondary text-body rounded-pill ms-2 small">{{ ucfirst($t->statut) }}</span>
                        </div>
                        <div class="text-body-secondary small">
                            {{ $t->date_effet ? \Carbon\Carbon::parse($t->date_effet)->format('d/m/Y') : $t->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    @endif

</div>
@endsection
