@extends('layouts.app')

@section('title', 'Messagerie')

@section('content')
<div class="container py-4"><div class="d-flex justify-content-between align-items-center mb-4"><div><p class="text-uppercase small fw-bold text-warning mb-1">Communication</p><h1 class="h3">Messagerie</h1></div></div><div class="list-group shadow-sm">@forelse($conversations as $conversation)<a href="{{ route('messages.conversation', $conversation->first()->expediteur_id === auth()->id() ? $conversation->first()->destinataire : $conversation->first()->expediteur) }}" class="list-group-item list-group-item-action py-3"><div class="d-flex justify-content-between"><strong>{{ ($conversation->first()->expediteur_id === auth()->id() ? $conversation->first()->destinataire : $conversation->first()->expediteur)->prenom ?? ($conversation->first()->expediteur_id === auth()->id() ? $conversation->first()->destinataire : $conversation->first()->expediteur)->name }}</strong><small class="text-secondary">{{ $conversation->first()->created_at->format('d/m/Y H:i') }}</small></div><div class="text-secondary text-truncate">{{ $conversation->first()->contenu }}</div></a>@empty<div class="text-center text-secondary py-5">Aucune conversation.</div>@endforelse</div></div>
@endsection
