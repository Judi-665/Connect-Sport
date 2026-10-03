@extends(auth()->check() && auth()->user()->role === 'agent' ? 'layouts.agent' : 'layouts.app')

@section('title', 'Annuaire des agents')

@if(auth()->check() && auth()->user()->role === 'agent')
    @section('page-title', 'Annuaire des agents')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <p class="text-uppercase small fw-bold text-primary mb-1">Réseau ConnectSport</p>
            <h1 class="h3 mb-1">Annuaire des agents</h1>
            <p class="directory-muted mb-0">Trouvez un agent accrédité pour accompagner votre carrière.</p>
        </div>
    </div>

    <div class="row g-3">
        @forelse($agents as $agent)
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('agents.show', $agent) }}" class="text-decoration-none text-reset">
                    <div class="cs-card h-100 p-4">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <div>
                                <h2 class="h5 mb-1">{{ $agent->nomComplet() }}</h2>
                                <div class="text-muted small">{{ $agent->agence ?: 'Agent indépendant' }}</div>
                            </div>
                            @if($agent->mise_en_avant)
                                <span class="badge bg-warning text-dark align-self-start"><i class="bi bi-star-fill me-1"></i>Recommandé</span>
                            @endif
                        </div>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $agent->ville }}, {{ $agent->pays }}</div>
                        <div class="text-muted small mt-2"><i class="bi bi-people me-1"></i>{{ $agent->joueurs()->wherePivot('statut', 'actif')->count() }} joueur(s) représenté(s)</div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12"><div class="cs-card p-5 text-center directory-muted">Aucun agent accrédité disponible.</div></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $agents->links() }}</div>
</div>
@endsection
