@extends('layouts.app')

@section('title', 'Mes candidatures — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="row align-items-end mb-4 g-3">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
                    Carrière
                </span>
            </div>
            <h1 class="fw-black mb-0" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
                Mes candidatures
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('clubs.index') }}" class="btn btn-warning fw-bold rounded-3 px-4">
                <i class="bi bi-search me-2"></i>Trouver un club
            </a>
        </div>
    </div>

    @if($candidatures->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-send text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-1">Vous n'avez postulé à aucun club.</p>
            <a href="{{ route('clubs.index') }}" class="btn btn-outline-warning rounded-3 fw-semibold mt-2">
                Parcourir les clubs
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($candidatures as $candidature)
            @php
                $statutConfig = [
                    'en_attente' => ['label' => 'En attente', 'bg' => 'rgba(249,115,22,.12)', 'color' => '#F97316'],
                    'acceptee'   => ['label' => 'Acceptée',   'bg' => 'rgba(34,197,94,.12)',  'color' => '#22C55E'],
                    'refusee'    => ['label' => 'Refusée',    'bg' => 'rgba(239,68,68,.12)',  'color' => '#EF4444'],
                    'annulee'    => ['label' => 'Annulée',    'bg' => 'rgba(100,116,139,.12)','color' => '#64748B'],
                ][$candidature->statut];
            @endphp
            <div class="rounded-4 border p-3 p-md-4 d-flex align-items-center gap-3">
                @if($candidature->club->logo)
                    <img src="{{ Storage::url($candidature->club->logo) }}" class="rounded-3 flex-shrink-0"
                         width="48" height="48" style="object-fit:cover;">
                @else
                    <div class="rounded-3 fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(91,155,213,.12);color:#1A56A0;
                                font-family:'Bebas Neue',sans-serif;font-size:18px;">
                        {{ strtoupper(substr($candidature->club->nom, 0, 2)) }}
                    </div>
                @endif
                <div class="flex-grow-1 overflow-hidden">
                    <h5 class="fw-bold text-truncate mb-0" style="font-size:.9rem;">{{ $candidature->club->nom }}</h5>
                    <div class="text-body-secondary" style="font-size:.75rem;">
                        {{ $candidature->poste_propose ?? 'Poste non précisé' }}
                        &bull; {{ $candidature->created_at->format('d/m/Y') }}
                    </div>
                </div>
                <span class="badge rounded-pill flex-shrink-0"
                      style="font-size:.68rem;background:{{ $statutConfig['bg'] }};color:{{ $statutConfig['color'] }};">
                    {{ $statutConfig['label'] }}
                </span>
                @if($candidature->estEnAttente())
                <form action="{{ route('joueur.candidatures.annuler', $candidature) }}" method="POST"
                      onsubmit="return confirm('Annuler cette candidature ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Annuler</button>
                </form>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $candidatures->links() }}</div>
    @endif

</div>
@endsection