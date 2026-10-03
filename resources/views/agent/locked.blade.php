{{-- resources/views/agent/locked.blade.php --}}
@extends('layouts.agent')

@section('title', 'Fonctionnalité verrouillée')
@section('page-title', 'Accès restreint')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card text-center py-5">
    <i class="bi bi-lock fs-1 text-warning mb-3"></i>
    <h2 class="h4 mb-2">Fonctionnalité réservée au plan {{ ucfirst($planRequis) }}</h2>
    <p class="text-secondary mb-4">Passez à un plan supérieur pour débloquer cette fonctionnalité.</p>
    <a href="{{ route('agent.abonnement.choisir') }}" class="btn-cs btn-cs-primary">Voir les plans</a>
</div>
@endsection