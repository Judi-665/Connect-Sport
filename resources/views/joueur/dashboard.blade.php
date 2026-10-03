@extends('layouts.app')

@section('title', 'Dashboard joueur')

@section('content')
<div class="container-fluid py-4">
    <div class="px-2 px-lg-3">

            {{-- Header (infos utilisateur) --}}
            @include('joueur.partials._header')

            {{-- Salutation dynamique --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    @php
                        $heure = (int) date('H');
                        $salutation = match(true) {
                            $heure >= 5 && $heure < 12 => 'Bonjour',
                            $heure >= 12 && $heure < 18 => 'Bon après-midi',
                            default => 'Bonsoir'
                        };
                    @endphp
                    <h1 class="h2 fw-bold mb-1">{{ $salutation }}, {{ auth()->user()->prenom ?? auth()->user()->name }}</h1>
                    <p class="text-muted small mb-0">Bienvenue sur votre espace joueur</p>
                </div>
                <div>
                    <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">{{ $joueur->club->nom ?? 'Sans club' }}</span>
                </div>
            </div>

            {{-- Cartes statistiques (KPI) --}}
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Matchs joués</div>
                                <h2 class="fw-bold mt-2 mb-0">{{ $stats['matchs'] ?? 0 }}</h2>
                            </div>
                            <i class="bi bi-trophy fs-1 text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Buts</div>
                                <h2 class="fw-bold mt-2 mb-0">{{ $stats['buts'] ?? 0 }}</h2>
                            </div>
                            <i class="bi bi-bullseye fs-1 text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Passes décisives</div>
                                <h2 class="fw-bold mt-2 mb-0">{{ $stats['passes'] ?? 0 }}</h2>
                            </div>
                            <i class="bi bi-eye fs-1 text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Note moyenne</div>
                                <h2 class="fw-bold mt-2 mb-0">{{ number_format($stats['note'] ?? 0, 1) }}</h2>
                            </div>
                            <i class="bi bi-star fs-1 text-warning opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row principale --}}
            <div class="row g-4">

                {{-- Colonne gauche : notifications --}}
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h5 class="fw-bold"><i class="bi bi-bell text-warning me-2"></i>Notifications récentes</h5>
                        </div>
                        <div class="card-body pt-3">
                            @forelse($notifications as $notif)
                                <div class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                        <i class="bi bi-envelope text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $notif->data['titre'] ?? $notif->data['message'] ?? 'Nouvelle notification' }}</div>
                                        <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">Aucune notification</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Colonne droite : prochains événements --}}
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h5 class="fw-bold"><i class="bi bi-calendar-event text-warning me-2"></i>Prochains événements</h5>
                        </div>
                        <div class="card-body pt-3">
                            @forelse($evenements as $event)
                                <div class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                        <i class="bi bi-calendar-check text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $event->titre }}</div>
                                        <div class="text-muted small">{{ $event->debut_at?->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">Aucun événement à venir</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <a href="{{ route('joueur.carriere.index') }}" class="btn btn-outline-warning rounded-pill px-4">
                                    <i class="bi bi-graph-up me-1"></i> Carrière
                                </a>
                                <a href="{{ route('joueur.agenda.index') }}" class="btn btn-outline-warning rounded-pill px-4">
                                    <i class="bi bi-chat-dots me-1"></i> Agenda
                                </a>
                                <a href="{{ route('joueur.transferts.index') }}" class="btn btn-outline-warning rounded-pill px-4">
                                    <i class="bi bi-arrow-left-right me-1"></i> Transferts
                                </a>
                                <a href="{{ route('joueur.opportunites.index') }}" class="btn btn-outline-warning rounded-pill px-4">
                                    <i class="bi bi-lightning me-1"></i> Opportunités
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div>
</div>
@endsection