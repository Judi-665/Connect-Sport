@extends('layouts.app')

@section('title', 'Candidatures reçues — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Recrutement
        </span>
    </div>
    <h1 class="fw-black mb-1" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
        Candidatures reçues
    </h1>
    <p class="text-body-secondary mb-4" style="font-size:.88rem;">
        Joueurs ayant demandé à rejoindre votre club.
    </p>

    @if($candidatures->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-inbox text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-1">Aucune candidature pour le moment.</p>
            <p class="text-body-secondary mb-0" style="font-size:.84rem;">
                Les joueurs intéressés par votre club apparaîtront ici.
            </p>
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
            <a href="{{ route('club.candidatures.show', $candidature) }}"
               class="text-decoration-none text-body cs-candidature-card d-block rounded-4 border p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;background:rgba(249,115,22,.12);color:#F97316;
                                font-family:'Bebas Neue',sans-serif;font-size:18px;">
                        {{ strtoupper(substr($candidature->joueur->user->name, 0, 2)) }}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h5 class="fw-bold text-truncate mb-0" style="font-size:.9rem;">
                            {{ $candidature->joueur->user->name }}
                        </h5>
                        <div class="text-body-secondary" style="font-size:.75rem;">
                            {{ $candidature->poste_propose ?? 'Poste non précisé' }}
                            &bull; {{ $candidature->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <span class="badge rounded-pill flex-shrink-0"
                          style="font-size:.68rem;background:{{ $statutConfig['bg'] }};color:{{ $statutConfig['color'] }};">
                        {{ $statutConfig['label'] }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $candidatures->links() }}</div>
    @endif

</div>

<style>
.cs-candidature-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
.cs-candidature-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(249,115,22,.1); border-color: #F97316 !important; }
</style>
@endsection