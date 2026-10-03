@extends('layouts.app')

@section('title', 'Demander un transfert — Connect Sport')

@section('content')
<div class="container py-5" style="max-width:600px;">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Transfert
        </span>
    </div>
    <h1 class="fw-black mb-2" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(26px,4vw,38px);letter-spacing:.04em;">
        Demander un transfert
    </h1>
    <p class="text-body-secondary mb-4" style="font-size:.84rem;">
        Votre demande sera soumise à la validation de votre club actuel, puis du club de destination.
    </p>

    <div class="rounded-4 border p-4">
        <form action="{{ route('joueur.transferts.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Club de destination</label>
                <select name="club_destinataire_id" class="form-select rounded-3" required>
                    <option value="">Sélectionner un club</option>
                    @foreach($clubs as $c)
                        <option value="{{ $c->id }}" @selected(old('club_destinataire_id') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
                @error('club_destinataire_id')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Type</label>
                <select name="type" class="form-select rounded-3" required>
                    <option value="definitif">Transfert définitif</option>
                    <option value="pret">Prêt</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Message (optionnel)</label>
                <textarea name="note_joueur" class="form-control rounded-3" rows="4" maxlength="500">{{ old('note_joueur') }}</textarea>
            </div>

            <button type="submit" class="btn btn-warning fw-bold rounded-3 px-4">
                <i class="bi bi-send me-2"></i>Envoyer la demande
            </button>
        </form>
    </div>

</div>
@endsection