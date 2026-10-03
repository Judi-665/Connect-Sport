@extends('layouts.app')
@section('title', $evenement->titre . ' — ' . $evenement->club->nom)

@section('content')
<div class="container py-5">

    <a href="{{ route('joueur.agenda.index') }}" class="text-muted small text-decoration-none mb-3 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> Retour à l'agenda
    </a>

    <div class="row">
        <div class="col-lg-8">

            {{-- Fiche principale --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        @php
                            $typeIcon = match($evenement->type) {
                                'match'        => 'bi-trophy',
                                'entrainement' => 'bi-people',
                                default        => 'bi-calendar-event',
                            };
                            $typeColor = match($evenement->type) {
                                'match'        => '#F97316',
                                'entrainement' => '#10b981',
                                default        => '#F97316',
                            };
                        @endphp
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:52px;height:52px;background:rgba(249,115,22,.12);">
                            <i class="bi {{ $typeIcon }} fs-4" style="color:{{ $typeColor }};"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-0">{{ $evenement->titre }}</h2>
                            <div class="d-flex gap-2 flex-wrap mt-1">
                                <span class="badge @if($evenement->type === 'match') bg-warning text-dark @elseif($evenement->type === 'entrainement') bg-success @else bg-info @endif rounded-pill">
                                    {{ ucfirst($evenement->type) }}
                                </span>
                                @if($evenement->estTermine())
                                    <span class="badge bg-secondary rounded-pill">Terminé</span>
                                @else
                                    <span class="badge bg-success rounded-pill">À venir</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 py-4">
                    {{-- Date et lieu --}}
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-start mb-3">
                                <i class="bi bi-calendar-event text-warning flex-shrink-0 mt-1"></i>
                                <div>
                                    <div class="text-muted small fw-semibold">Date et heure</div>
                                    <div class="fw-bold">
                                        {{ $evenement->debut_at->format('d/m/Y à H:i') }}
                                        @if($evenement->fin_at)
                                            <br><span class="text-muted small">→ {{ $evenement->fin_at->format('H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($evenement->lieu)
                        <div class="col-md-6">
                            <div class="d-flex gap-2 align-items-start mb-3">
                                <i class="bi bi-geo-alt text-warning flex-shrink-0 mt-1"></i>
                                <div>
                                    <div class="text-muted small fw-semibold">Lieu</div>
                                    <div class="fw-bold">{{ $evenement->lieu }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Infos Match --}}
                    @if($evenement->type === 'match')
                        <div class="border-top pt-4 mb-4">
                            <h5 class="h6 fw-bold mb-3">
                                <i class="bi bi-trophy me-2" style="color:#F97316;"></i>Infos du match
                            </h5>
                            <div class="row">
                                @if($evenement->adversaire_nom)
                                    <div class="col-md-6">
                                        <div class="d-flex gap-2 mb-3">
                                            <i class="bi bi-shield text-warning flex-shrink-0 mt-1"></i>
                                            <div>
                                                <div class="text-muted small fw-semibold">Adversaire</div>
                                                <div class="fw-bold">{{ $evenement->adversaire_nom }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($evenement->domicile_exterieur)
                                    <div class="col-md-6">
                                        <div class="d-flex gap-2 mb-3">
                                            <i class="bi bi-compass text-warning flex-shrink-0 mt-1"></i>
                                            <div>
                                                <div class="text-muted small fw-semibold">Localisation</div>
                                                <div class="fw-bold">{{ ucfirst($evenement->domicile_exterieur) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($evenement->estTermine())
                                <div class="mt-4 p-3 rounded-3" style="background:rgba(249,115,22,.1);">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="text-muted small">Nous</div>
                                            <div class="display-6 fw-bold">{{ $evenement->score_nous ?? '—' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Eux</div>
                                            <div class="display-6 fw-bold">{{ $evenement->score_eux ?? '—' }}</div>
                                        </div>
                                    </div>
                                    @if($evenement->resultat)
                                        <div class="text-center mt-3">
                                            <span class="badge @if($evenement->resultat === 'victoire') bg-success @elseif($evenement->resultat === 'defaite') bg-danger @else bg-secondary @endif rounded-pill px-3 py-2">
                                                {{ ucfirst($evenement->resultat) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info rounded-3 mt-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Le score et le résultat seront disponibles après le match.
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Description --}}
                    @if($evenement->description)
                        <div class="border-top pt-4">
                            <h5 class="h6 fw-bold mb-2">Description</h5>
                            <p class="text-muted">{{ nl2br(e($evenement->description)) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Médias --}}
            @if($evenement->medias->count() > 0)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                        <h5 class="h6 fw-bold mb-0">
                            <i class="bi bi-images me-2" style="color:#F97316;"></i>Médias ({{ $evenement->medias->count() }})
                        </h5>
                    </div>
                    <div class="card-body px-4 py-4">
                        <div class="row g-3">
                            @foreach($evenement->medias as $media)
                                <div class="col-sm-6 col-md-4">
                                    <a href="{{ $media->url() }}" target="_blank"
                                       class="d-block overflow-hidden rounded-3" style="height:150px;">
                                        @if(in_array(strtolower(pathinfo($media->fichier, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ $media->url() }}" alt="Média" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background:#f1f5f9;">
                                                <i class="bi bi-file-earmark-arrow-down fs-3 text-muted"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Statistiques du match --}}
            @if($evenement->statistiques->count() > 0)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                        <h5 class="h6 fw-bold mb-0">
                            <i class="bi bi-bar-chart me-2" style="color:#F97316;"></i>Statistiques
                        </h5>
                    </div>
                    <div class="card-body px-4 py-4">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Joueur</th>
                                        <th class="text-center">Buts</th>
                                        <th class="text-center">Passes</th>
                                        <th class="text-center">Cartons</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evenement->statistiques->groupBy('joueur_id') as $stats)
                                        @php $j = $stats->first()->joueur; @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $j->nomComplet() }}</td>
                                            <td class="text-center">{{ $stats->sum('buts') }}</td>
                                            <td class="text-center">{{ $stats->sum('passes_decisives') }}</td>
                                            <td class="text-center">
                                                @php $cartons = $stats->sum('cartons_jaunes') + $stats->sum('cartons_rouges'); @endphp
                                                {{ $cartons > 0 ? $cartons : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar infos (lecture seule, pas d'actions de gestion) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:80px;">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                        <i class="bi bi-calendar-range text-warning fs-5"></i>
                        <div>
                            <div class="text-muted small">{{ $evenement->debut_at->format('d M Y') }}</div>
                            <div class="fw-bold">{{ $evenement->debut_at->format('H:i') }}</div>
                        </div>
                    </div>
                    @if($evenement->lieu)
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt text-warning fs-5"></i>
                            <div>
                                <div class="text-muted small">Lieu</div>
                                <div class="fw-bold small">{{ $evenement->lieu }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection