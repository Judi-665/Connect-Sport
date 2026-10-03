@extends('layouts.app')

@section('title', 'Mes transferts — Connect Sport')

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
                Mes transferts
            </h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('joueur.transferts.create') }}" class="btn btn-warning fw-bold rounded-3 px-4">
                <i class="bi bi-send me-2"></i>Demander un transfert
            </a>
        </div>
    </div>

    @if($transferts->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-arrow-left-right text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-1">Aucun transfert pour le moment.</p>
            <p class="text-body-secondary mb-0" style="font-size:.84rem;">
                Les transferts initiés par votre club ou par vous-même apparaîtront ici.
            </p>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($transferts as $transfert)
            @php
                $statutConfig = [
                    'en_attente'     => ['label' => 'En attente',     'bg' => 'rgba(249,115,22,.12)', 'color' => '#F97316'],
                    'en_negociation' => ['label' => 'En négociation', 'bg' => 'rgba(91,155,213,.12)',  'color' => '#1A56A0'],
                    'accepte'        => ['label' => 'Accepté',        'bg' => 'rgba(34,197,94,.12)',   'color' => '#22C55E'],
                    'refuse'         => ['label' => 'Refusé',         'bg' => 'rgba(239,68,68,.12)',   'color' => '#EF4444'],
                    'annule'         => ['label' => 'Annulé',         'bg' => 'rgba(100,116,139,.12)', 'color' => '#64748B'],
                ][$transfert->statut];
            @endphp
            <a href="{{ route('joueur.transferts.show', $transfert) }}"
               class="text-decoration-none text-body cs-transfert-card d-block rounded-4 border p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-bold" style="font-size:.88rem;">{{ $transfert->clubSource->nom }}</span>
                        <i class="bi bi-arrow-right text-body-secondary"></i>
                        <span class="fw-bold" style="font-size:.88rem;">{{ $transfert->clubDestinataire->nom }}</span>
                    </div>
                    <span class="badge rounded-pill flex-shrink-0"
                          style="font-size:.68rem;background:{{ $statutConfig['bg'] }};color:{{ $statutConfig['color'] }};">
                        {{ $statutConfig['label'] }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                        {{ ucfirst($transfert->type) }}
                    </span>
                    @if($transfert->initiee_par === 'joueur')
                    <span class="badge rounded-pill" style="font-size:.68rem;background:rgba(249,115,22,.12);color:#F97316;">
                        <i class="bi bi-person-fill me-1"></i>Ma demande
                    </span>
                    @endif
                    <span class="text-body-secondary" style="font-size:.75rem;">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $transfert->date_effet?->format('d/m/Y') ?? 'Date non fixée' }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $transferts->links() }}</div>
    @endif

</div>

<style>
.cs-transfert-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
.cs-transfert-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(249,115,22,.1); border-color: #F97316 !important; }
</style>
@endsection