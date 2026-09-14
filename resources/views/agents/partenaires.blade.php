@extends('layouts.agent')

@section('title', 'Clubs partenaires')
@section('page-title', 'Clubs partenaires')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card"><div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-building"></i>Clubs partenaires</div></div><div class="table-responsive"><table class="cs-table"><thead><tr><th>Club</th><th>Ville / pays</th><th>Contact</th><th>Action</th></tr></thead><tbody>@forelse($clubs as $club)<tr><td><strong>{{ $club->nom }}</strong></td><td>{{ $club->ville ?? '—' }} / {{ $club->pays ?? '—' }}</td><td>{{ $club->user?->email ?? $club->telephone ?? '—' }}</td><td>@if($club->user)<a href="{{ route('messages.conversation', $club->user) }}" class="btn-cs btn-cs-ghost"><i class="bi bi-chat-dots"></i>Contacter</a>@endif</td></tr>@empty<tr><td colspan="4"><div class="cs-empty"><i class="bi bi-building"></i><p>Aucun club partenaire enregistré.</p></div></td></tr>@endforelse</tbody></table></div>@if($clubs->hasPages())<div class="p-3">{{ $clubs->links() }}</div>@endif</div>
@endsection
