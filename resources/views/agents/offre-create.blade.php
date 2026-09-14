@extends('layouts.agent')

@section('title', 'Nouvelle offre de transfert')
@section('page-title', 'Soumettre une offre')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
@php
    $clubsSources = $joueurs->pluck('club')->filter()->unique('id')->sortBy('nom');
@endphp
<div class="cs-card"><div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-send"></i>Nouvelle offre de transfert</div></div><div class="cs-card-body">
    <form method="POST" action="{{ route('agent.transferts.store') }}" class="row g-3" id="offre-form">
        @csrf
        <div class="col-md-6"><label for="club_source_id" class="form-label">Club source</label><select id="club_source_id" name="club_source_id" class="form-select" required><option value="">Choisir un club</option>@foreach($clubsSources as $club)<option value="{{ $club->id }}" @selected(old('club_source_id') == $club->id)>{{ $club->nom }} @if($club->ville)({{ $club->ville }})@endif</option>@endforeach</select><div class="form-text">Les joueurs représentés de ce club seront affichés ensuite.</div></div>
        <div class="col-md-6"><label for="joueur_id" class="form-label">Joueur représenté</label><select id="joueur_id" name="joueur_id" class="form-select" required disabled><option value="">Choisir d'abord un club</option>@foreach($joueurs as $joueur)<option value="{{ $joueur->id }}" data-club-id="{{ $joueur->club_id }}" @selected(old('joueur_id') == $joueur->id)>{{ $joueur->nomComplet() }} - {{ $joueur->club?->nom ?? 'Sans club' }}</option>@endforeach</select><div id="joueur-help" class="form-text">Sélectionnez un club pour afficher ses joueurs.</div></div>
        <div class="col-md-6"><label class="form-label">Club destinataire</label><select name="club_destinataire_id" class="form-select" required><option value="">Choisir un club</option>@foreach($clubs as $club)<option value="{{ $club->id }}">{{ $club->nom }} @if($club->ville)({{ $club->ville }})@endif</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Type</label><select name="type" class="form-select" required><option value="definitif">Transfert définitif</option><option value="pret">Prêt</option><option value="essai">Essai</option></select></div>
        <div class="col-md-4"><label class="form-label">Montant</label><input type="number" min="0" step="0.01" name="montant" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Devise</label><select name="devise" class="form-select"><option>XOF</option><option>EUR</option><option>USD</option></select></div>
        <div class="col-md-6"><label class="form-label">Date d'effet</label><input type="date" name="date_effet" min="{{ date('Y-m-d') }}" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Fin du prêt</label><input type="date" name="date_fin_pret" class="form-control"></div>
        <div class="col-12"><div class="form-check"><input type="checkbox" name="montant_confidentiel" value="1" class="form-check-input" id="confidentiel"><label class="form-check-label" for="confidentiel">Garder le montant confidentiel</label></div></div>
        <div class="col-12"><label class="form-label">Message aux clubs</label><textarea name="agent_note" rows="4" maxlength="2000" class="form-control" placeholder="Conditions, contexte et proposition de l'agent"></textarea></div>
        <div class="col-12 d-flex justify-content-end gap-2"><a href="{{ route('agent.transferts') }}" class="btn-cs btn-cs-ghost">Annuler</a><button class="btn-cs btn-cs-primary"><i class="bi bi-send"></i>Soumettre l'offre</button></div>
    </form>
</div></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clubSelect = document.getElementById('club_source_id');
        const playerSelect = document.getElementById('joueur_id');
        const playerHelp = document.getElementById('joueur-help');
        const previousPlayer = @json(old('joueur_id'));

        function filterPlayers() {
            const clubId = clubSelect.value;
            let visiblePlayers = 0;

            Array.from(playerSelect.options).forEach(function (option, index) {
                if (index === 0) return;
                const visible = Boolean(clubId && option.dataset.clubId === clubId);
                option.hidden = !visible;
                option.disabled = !visible;
                if (visible) visiblePlayers++;
            });

            playerSelect.disabled = !clubId || visiblePlayers === 0;
            if (!clubId) {
                playerSelect.value = '';
                playerHelp.textContent = 'Sélectionnez un club pour afficher ses joueurs.';
            } else if (visiblePlayers === 0) {
                playerSelect.value = '';
                playerHelp.textContent = 'Aucun joueur représenté actif dans ce club.';
            } else {
                playerHelp.textContent = visiblePlayers + ' joueur(s) représenté(s) disponible(s).';
                if (previousPlayer && playerSelect.querySelector('option[value="' + previousPlayer + '"]:not([disabled])')) {
                    playerSelect.value = previousPlayer;
                }
            }
        }

        clubSelect.addEventListener('change', filterPlayers);
        filterPlayers();
    });
</script>
@endpush
