@extends('layouts.agent')

@section('title', 'Profil accrédité')
@section('page-title', 'Profil accrédité')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card"><div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-person-badge"></i>Informations professionnelles</div></div><div class="cs-card-body">
    <div class="mb-3"><span class="text-secondary">Numéro d'accréditation</span><strong class="d-block">{{ $agent->numero_accreditation }}</strong></div>
    <form method="POST" action="{{ route('agent.profil.update') }}" class="row g-3">
        @csrf @method('PUT')
        <div class="col-md-6"><label class="form-label">Agence</label><input name="agence" class="form-control" value="{{ old('agence', $agent->agence) }}" required></div>
        <div class="col-md-6"><label class="form-label">Téléphone</label><input name="telephone" class="form-control" value="{{ old('telephone', $agent->telephone) }}" required></div>
        <div class="col-md-6"><label class="form-label">Ville</label><input name="ville" class="form-control" value="{{ old('ville', $agent->ville) }}" required></div>
        <div class="col-md-6"><label class="form-label">Pays</label><input name="pays" class="form-control" value="{{ old('pays', $agent->pays) }}" required></div>
        <div class="col-12"><label class="form-label">Site web</label><input type="url" name="site_web" class="form-control" value="{{ old('site_web', $agent->site_web) }}"></div>
        <div class="col-12"><label class="form-label">Biographie professionnelle</label><textarea name="bio" class="form-control" rows="5">{{ old('bio', $agent->bio) }}</textarea></div>
        <div class="col-12"><button class="btn-cs btn-cs-primary"><i class="bi bi-check2"></i>Enregistrer</button></div>
    </form>
</div></div>
@endsection
