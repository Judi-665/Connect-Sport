@extends('layouts.app')

@section('title', 'Candidature — Connect Sport')

@section('content')
<div class="container py-5" style="max-width:720px;">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Candidature
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(26px,4vw,38px);letter-spacing:.04em;">
        {{ $candidature->joueur->user->name }}
    </h1>

    <div class="rounded-4 border p-4 mb-4">
        <div class="row g-3">
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Poste proposé</div>
                <div class="fw-semibold" style="font-size:.88rem;">{{ $candidature->poste_propose ?? '—' }}</div>
            </div>
            <div class="col-6">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Reçue le</div>
                <div class="fw-semibold" style="font-size:.88rem;">{{ $candidature->created_at->format('d/m/Y à H:i') }}</div>
            </div>
            <div class="col-12">
                <div class="text-body-secondary text-uppercase" style="font-size:.68rem;letter-spacing:.08em;">Message du joueur</div>
                <p class="mb-0 mt-1" style="font-size:.86rem;line-height:1.6;">
                    {{ $candidature->message ?? 'Aucun message.' }}
                </p>
            </div>
        </div>
    </div>

    @if($candidature->estEnAttente())
        <div class="d-flex gap-2">
            <form action="{{ route('club.candidatures.accepter', $candidature) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning fw-semibold rounded-3 px-4">
                    <i class="bi bi-check-circle me-2"></i>Accepter
                </button>
            </form>
            <button type="button" class="btn btn-outline-danger fw-semibold rounded-3 px-4"
                    data-bs-toggle="modal" data-bs-target="#refuserModal">
                <i class="bi bi-x-circle me-2"></i>Refuser
            </button>
        </div>

        <div class="modal fade" id="refuserModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('club.candidatures.refuser', $candidature) }}" method="POST">
                    @csrf
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold" style="font-size:1rem;">Refuser la candidature</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Motif (optionnel)</label>
                            <textarea name="note_club" class="form-control rounded-3" rows="3"></textarea>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger rounded-3">Confirmer le refus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <span class="badge rounded-pill" style="font-size:.72rem;background:var(--bs-tertiary-bg);">
            Déjà traitée — {{ ucfirst(str_replace('_', ' ', $candidature->statut)) }}
        </span>
    @endif

</div>
@endsection