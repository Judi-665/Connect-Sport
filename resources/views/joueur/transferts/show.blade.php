@extends('layouts.app')

@section('title', 'Détail transfert — Connect Sport')

@section('content')
<div class="container py-5" style="max-width:720px;">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Transfert
        </span>
    </div>
    <h1 class="fw-black mb-2 d-flex align-items-center gap-2 flex-wrap"
        style="font-family:'Bebas Neue',sans-serif;font-size:clamp(24px,4vw,36px);letter-spacing:.04em;">
        {{ $transfert->clubSource->nom }}
        <i class="bi bi-arrow-right text-warning" style="font-size:.7em;"></i>
        {{ $transfert->clubDestinataire->nom }}
    </h1>

    @if($transfert->initiee_par === 'joueur')
    <span class="badge rounded-pill mb-4" style="font-size:.7rem;background:rgba(249,115,22,.12);color:#F97316;">
        <i class="bi bi-person-fill me-1"></i>Demande initiée par vous
    </span>
    @else
    <span class="badge rounded-pill mb-4" style="font-size:.7rem;background:var(--bs-tertiary-bg);">
        Initié par le club
    </span>
    @endif

    @php
        $statutConfig = [
            'en_attente'     => ['label' => 'En attente',     'bg' => 'rgba(249,115,22,.12)', 'color' => '#F97316'],
            'en_negociation' => ['label' => 'En négociation', 'bg' => 'rgba(91,155,213,.12)',  'color' => '#1A56A0'],
            'accepte'        => ['label' => 'Accepté',        'bg' => 'rgba(34,197,94,.12)',   'color' => '#22C55E'],
            'refuse'         => ['label' => 'Refusé',         'bg' => 'rgba(239,68,68,.12)',   'color' => '#EF4444'],
            'annule'         => ['label' => 'Annulé',         'bg' => 'rgba(100,116,139,.12)', 'color' => '#64748B'],
        ][$transfert->statut];
    @endphp

    <div class="rounded-4 border p-4 mb-4">
        <div class="row g-3">
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Statut</div>
                <span class="badge rounded-pill mt-1" style="font-size:.72rem;background:{{ $statutConfig['bg'] }};color:{{ $statutConfig['color'] }};">
                    {{ $statutConfig['label'] }}
                </span>
            </div>
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Type</div>
                <div class="fw-semibold mt-1" style="font-size:.86rem;">{{ ucfirst($transfert->type) }}</div>
            </div>
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Date d'effet</div>
                <div class="fw-semibold mt-1" style="font-size:.86rem;">{{ $transfert->date_effet?->format('d/m/Y') ?? '—' }}</div>
            </div>
            @if($transfert->type === 'pret')
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Fin de prêt</div>
                <div class="fw-semibold mt-1" style="font-size:.86rem;">{{ $transfert->date_fin_pret?->format('d/m/Y') ?? '—' }}</div>
            </div>
            @endif
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Montant</div>
                <div class="fw-semibold mt-1" style="font-size:.86rem;">{{ $transfert->montantAffiche() }}</div>
            </div>
            @if($transfert->agent)
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Agent</div>
                <div class="fw-semibold mt-1" style="font-size:.86rem;">{{ $transfert->agent->user->name }}</div>
            </div>
            @endif
            @if($transfert->note_joueur)
            <div class="col-12">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Votre message</div>
                <p class="mb-0 mt-1" style="font-size:.84rem;line-height:1.6;">{{ $transfert->note_joueur }}</p>
            </div>
            @endif
        </div>
    </div>

    <a href="{{ route('joueur.transferts.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 fw-semibold">
        <i class="bi bi-arrow-left me-2"></i>Retour à mes transferts
    </a>

</div>
@endsection