@extends('layouts.app')

@section('title', 'Postuler — ' . $club->nom)

@section('content')
<div class="container py-5" style="max-width:600px;">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Candidature
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(26px,4vw,38px);letter-spacing:.04em;">
        Postuler chez {{ $club->nom }}
    </h1>

    <div class="rounded-4 border p-4">
        <form action="{{ route('joueur.candidatures.store', $club) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="poste_propose" class="form-label fw-semibold mb-1" style="font-size:.78rem;">
                    Poste souhaité
                </label>
                <input type="text" name="poste_propose" id="poste_propose"
                       class="form-control rounded-3" maxlength="60" value="{{ old('poste_propose') }}">
            </div>

            <div class="mb-4">
                <label for="message" class="form-label fw-semibold mb-1" style="font-size:.78rem;">
                    Message au club
                </label>
                <textarea name="message" id="message" class="form-control rounded-3" rows="4"
                          maxlength="1000">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn btn-warning fw-bold rounded-3 px-4">
                <i class="bi bi-send me-2"></i>Envoyer la candidature
            </button>
        </form>
    </div>

</div>
@endsection