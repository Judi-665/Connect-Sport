@extends('layouts.agent')

@section('title', 'Proposer un mandat — Connect Sport')
@section('page-title', 'Proposer un mandat')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="container py-5" style="max-width:600px;">

    <h1 class="h4 fw-bold mb-1">Proposer un mandat</h1>
    <p class="text-body-secondary mb-4" style="font-size:.86rem;">
        Représenter {{ $joueur->nomComplet() }}
    </p>

    <div class="card border rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('agent.joueurs.mandat.store', $joueur) }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:.78rem;">Début du mandat</label>
                        <input type="date" name="debut_mandat" class="form-control rounded-3" value="{{ old('debut_mandat', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:.78rem;">Fin du mandat</label>
                        <input type="date" name="fin_mandat" class="form-control rounded-3" value="{{ old('fin_mandat') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.78rem;">Commission (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="commission_pourcentage"
                           class="form-control rounded-3" value="{{ old('commission_pourcentage') }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:.78rem;">Conditions particulières (optionnel)</label>
                    <textarea name="note" class="form-control rounded-3" rows="3">{{ old('note') }}</textarea>
                </div>

                @error('debut_mandat')<div class="text-danger mb-2" style="font-size:.8rem;">{{ $message }}</div>@enderror
                @error('fin_mandat')<div class="text-danger mb-2" style="font-size:.8rem;">{{ $message }}</div>@enderror

                <button type="submit" class="btn btn-primary fw-semibold rounded-3 px-4">
                    Signer le mandat
                </button>
            </form>
        </div>
    </div>

</div>
@endsection