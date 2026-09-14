{{-- resources/views/club/transferts/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Proposer un transfert — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('club.dashboard') }}" class="text-warning text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('club.transferts.index') }}" class="text-warning text-decoration-none">Transferts</a>
            </li>
            <li class="breadcrumb-item active">Proposer un transfert</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:42px;height:42px;background:rgba(249,115,22,.12);">
                            <i class="bi bi-arrow-left-right text-warning fs-5"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-0">Proposer un transfert</h2>
                            <p class="text-muted small mb-0">{{ $club->nom }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('club.transferts.store') }}">
                        @csrf

                        {{-- Joueur --}}
                        <div class="mb-4">
                            <label for="joueur_id" class="form-label fw-semibold">
                                Joueur <span class="text-danger">*</span>
                            </label>
                            <select id="joueur_id" name="joueur_id"
                                    class="form-select rounded-3 @error('joueur_id') is-invalid @enderror"
                                    required>
                                <option value="" disabled @selected(!old('joueur_id'))>Choisir un joueur…</option>
                                @foreach($joueurs as $joueur)
                                    <option value="{{ $joueur->id }}" @selected(old('joueur_id') == $joueur->id)>
                                        {{ $joueur->nomComplet() }}
                                        @if($joueur->poste) — {{ ucfirst($joueur->poste) }} @endif
                                        @if($joueur->categorie) ({{ ucfirst($joueur->categorie) }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('joueur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Club destinataire --}}
                        <div class="mb-4">
                            <label for="club_destinataire_id" class="form-label fw-semibold">
                                Club destinataire <span class="text-danger">*</span>
                            </label>
                            <select id="club_destinataire_id" name="club_destinataire_id"
                                    class="form-select rounded-3 @error('club_destinataire_id') is-invalid @enderror"
                                    required>
                                <option value="" disabled @selected(!old('club_destinataire_id'))>Choisir le club destinataire…</option>
                                @foreach($clubs as $c)
                                    @if($c->id !== $club->id)
                                        <option value="{{ $c->id }}" @selected(old('club_destinataire_id') == $c->id)>
                                            {{ $c->nom }}
                                            @if($c->ville) — {{ $c->ville }} @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('club_destinataire_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Type + Agent --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">
                                    Type de transfert <span class="text-danger">*</span>
                                </label>
                                <select id="type" name="type"
                                        class="form-select rounded-3 @error('type') is-invalid @enderror"
                                        required>
                                    <option value="" disabled @selected(!old('type'))>Choisir…</option>
                                    <option value="definitif" @selected(old('type') === 'definitif')>Définitif</option>
                                    <option value="pret"      @selected(old('type') === 'pret')>Prêt</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="agent_id" class="form-label fw-semibold">
                                    Agent sportif
                                    <span class="text-muted fw-normal">(optionnel)</span>
                                </label>
                                <select id="agent_id" name="agent_id"
                                        class="form-select rounded-3 @error('agent_id') is-invalid @enderror">
                                    <option value="">Aucun agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" @selected(old('agent_id') == $agent->id)>
                                            {{ $agent->user->prenom ?? '' }} {{ $agent->user->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Montant + Devise --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label for="montant" class="form-label fw-semibold">Montant</label>
                                <input type="number" id="montant" name="montant"
                                       class="form-control rounded-3 @error('montant') is-invalid @enderror"
                                       value="{{ old('montant') }}" min="0" step="0.01"
                                       placeholder="0">
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="devise" class="form-label fw-semibold">
                                    Devise <span class="text-danger">*</span>
                                </label>
                                <select id="devise" name="devise"
                                        class="form-select rounded-3 @error('devise') is-invalid @enderror"
                                        required>
                                    <option value="XOF" @selected(old('devise','XOF') === 'XOF')>XOF (FCFA)</option>
                                    <option value="EUR" @selected(old('devise') === 'EUR')>EUR (€)</option>
                                    <option value="USD" @selected(old('devise') === 'USD')>USD ($)</option>
                                </select>
                                @error('devise')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input type="checkbox" id="montant_confidentiel"
                                           name="montant_confidentiel" value="1"
                                           class="form-check-input"
                                           @checked(old('montant_confidentiel'))>
                                    <label for="montant_confidentiel" class="form-check-label small fw-semibold">
                                        Montant confidentiel
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="date_effet" class="form-label fw-semibold">
                                    Date d'effet <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_effet" name="date_effet"
                                       class="form-control rounded-3 @error('date_effet') is-invalid @enderror"
                                       value="{{ old('date_effet') }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('date_effet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Date fin prêt (conditionnelle) --}}
                            <div class="col-md-6" id="datePretWrap" style="display:none;">
                                <label for="date_fin_pret" class="form-label fw-semibold">
                                    Date de fin du prêt <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_fin_pret" name="date_fin_pret"
                                       class="form-control rounded-3 @error('date_fin_pret') is-invalid @enderror"
                                       value="{{ old('date_fin_pret') }}">
                                @error('date_fin_pret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <label for="note_joueur" class="form-label fw-semibold">
                                Note / Observations
                                <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <textarea id="note_joueur" name="note_joueur"
                                      class="form-control rounded-3 @error('note_joueur') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Contexte du transfert, conditions particulières…">{{ old('note_joueur') }}</textarea>
                            @error('note_joueur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('club.transferts.index') }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                                <i class="bi bi-send me-1"></i> Soumettre la demande
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
/* Afficher/masquer date fin prêt selon type */
document.getElementById('type').addEventListener('change', function () {
    const wrap = document.getElementById('datePretWrap');
    const input = document.getElementById('date_fin_pret');
    if (this.value === 'pret') {
        wrap.style.display = '';
        input.required = true;
    } else {
        wrap.style.display = 'none';
        input.required = false;
        input.value = '';
    }
});

/* Restaurer l'état si old() existe */
document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('type').value;
    if (type === 'pret') {
        document.getElementById('datePretWrap').style.display = '';
        document.getElementById('date_fin_pret').required = true;
    }
});
</script>
@endpush
@endsection