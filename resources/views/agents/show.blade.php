@extends(auth()->check() && auth()->user()->role === 'agent' ? 'layouts.agent' : 'layouts.app')

@section('title', $agent->nomComplet())

@if(auth()->check() && auth()->user()->role === 'agent')
    @section('page-title', 'Profil agent')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@section('content')
<div class="container py-5">
    <a href="{{ route('agents.index') }}" class="btn btn-link px-0 mb-4"><i class="bi bi-arrow-left me-1"></i>Retour à l'annuaire</a>
    <div class="cs-card p-4 p-md-5">
        <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase small fw-bold text-primary mb-1">Agent accrédité</p>
                <h1 class="h3 mb-1">{{ $agent->nomComplet() }}</h1>
                <p class="directory-muted mb-0">{{ $agent->agence ?: 'Agent indépendant' }}</p>
            </div>
            @if($agent->mise_en_avant)
                <span class="badge bg-warning text-dark align-self-start"><i class="bi bi-star-fill me-1"></i>Agent recommandé</span>
            @endif
        </div>
        <div class="row g-3 mb-4">
            <div class="col-sm-4"><div class="border rounded p-3"><div class="text-muted small">Localisation</div><strong>{{ $agent->ville }}, {{ $agent->pays }}</strong></div></div>
            <div class="col-sm-4"><div class="border rounded p-3"><div class="text-muted small">Joueurs représentés</div><strong>{{ $agent->joueurs()->wherePivot('statut', 'actif')->count() }}</strong></div></div>
            <div class="col-sm-4"><div class="border rounded p-3"><div class="text-muted small">Accréditation</div><strong>{{ $agent->numero_accreditation }}</strong></div></div>
        </div>
        @if($agent->bio)<p class="mb-0">{{ $agent->bio }}</p>@endif
    </div>
</div>
@endsection
