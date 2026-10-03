@extends('layouts.agent')

@section('title', 'Opportunités')
@section('page-title', 'Opportunités')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
    <div class="mb-4">
        <div class="text-uppercase small fw-bold" style="color:var(--cs-orange);">Espace agent</div>
        <h1 class="h3 mb-1">Opportunités disponibles</h1>
        <p class="text-secondary mb-0">Placez un de vos joueurs représentés sur une opportunité publiée par un club.</p>
    </div>

    @if($joueurs->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-info-circle me-2"></i>Vous devez représenter au moins un joueur actif pour postuler à une opportunité.
        </div>
    @endif

    <div class="row g-3">
        @forelse($opportunites as $opp)
        <div class="col-12 col-md-6">
            <div class="cs-card h-100">
                <div class="cs-card-header">
                    <div class="cs-card-title">
                        @if($opp->mise_en_avant)<i class="bi bi-star-fill text-warning me-1"></i>@endif
                        {{ $opp->titre }}
                    </div>
                    <span class="cs-status">{{ ucfirst($opp->type) }}</span>
                </div>
                <div class="p-3">
                    <p class="text-secondary mb-2" style="font-size:.85rem;">{{ Str::limit($opp->description, 120) }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-3" style="font-size:.78rem;">
                        <span class="badge bg-light text-dark"><i class="bi bi-building me-1"></i>{{ $opp->club->nom }}</span>
                        <span class="badge bg-light text-dark"><i class="bi bi-geo-alt me-1"></i>{{ $opp->lieu }}, {{ $opp->pays }}</span>
                        <span class="badge bg-light text-dark"><i class="bi bi-calendar3 me-1"></i>Limite : {{ $opp->date_limite?->format('d/m/Y') }}</span>
                        @if($opp->poste_cible)
                        <span class="badge bg-light text-dark">{{ $opp->poste_cible }}</span>
                        @endif
                    </div>

                    @if($joueurs->isNotEmpty())
                    <form action="{{ route('agent.opportunites.postuler', $opp) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="joueur_id" class="form-select form-select-sm rounded-3" required>
                            <option value="">Sélectionner un joueur</option>
                            @foreach($joueurs as $j)
                                <option value="{{ $j->id }}">{{ $j->nomComplet() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-cs btn-cs-primary btn-sm text-nowrap">Postuler</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="cs-empty"><i class="bi bi-briefcase"></i><p>Aucune opportunité disponible pour le moment.</p></div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $opportunites->links() }}</div>
@endsection