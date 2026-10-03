@extends('layouts.app')

@section('title', 'Demandes de lien parent — Connect Sport')

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Demandes de lien parent</h1>
            <p class="text-muted mb-0">Un lien accepté autorise ce parent à consulter certaines informations de votre profil.</p>
        </div>
        <a href="{{ route('joueur.dashboard') }}" class="btn btn-outline-secondary rounded-pill">Retour au tableau de bord</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($demandes as $demande)
        <article class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="h6 fw-bold mb-1">{{ $demande->user?->prenom }} {{ $demande->user?->name }}</h2>
                    <p class="small text-muted mb-0">Lien déclaré : {{ ucfirst($demande->lien) }} · reçue le {{ $demande->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('joueur.parents.accepter', $demande) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-success rounded-pill" type="submit">Confirmer</button>
                    </form>
                    <form method="POST" action="{{ route('joueur.parents.refuser', $demande) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger rounded-pill" type="submit">Refuser</button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5 text-muted">Aucune demande en attente.</div>
        </div>
    @endforelse
</div>
@endsection
