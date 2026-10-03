{{-- resources/views/messages/inbox.blade.php --}}
@php
    $role = auth()->user()->role ?? 'guest';
    $layout = match($role) {
        'admin'  => 'layouts.app',
        'agent'  => 'layouts.agent',
        'parent' => 'layouts.app',
        default  => 'layouts.app',
    };
@endphp
@extends($layout)

@section('title', 'Messagerie — Connect Sport')

@if($role === 'agent')
    @section('page-title', 'Messagerie')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@push('styles')
<style>
.msg-inbox-wrap {
    max-width: 780px;
    margin: 0 auto;
}
.msg-conv-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    border: 1px solid transparent;
    transition: all .18s ease;
    position: relative;
}
.msg-conv-item:hover {
    background: var(--bs-tertiary-bg, #f8f9fa);
    border-color: var(--bs-border-color, #dee2e6);
    transform: translateX(2px);
}
.msg-conv-item + .msg-conv-item {
    border-top: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.08));
}
.msg-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
.msg-avatar-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: #fff;
    flex-shrink: 0;
    background: linear-gradient(135deg, #0D2E5C, #0D9488);
}
.msg-unread-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ef4444;
    flex-shrink: 0;
}
.msg-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--bs-secondary-color, #6c757d);
}
</style>
@endpush

@section('content')
@if($role === 'parent')
    @include('parent.partials._sidebar')
    <div style="padding-left:260px;min-height:100vh;padding-top:24px;padding-right:24px;padding-bottom:24px;">
@elseif($role === 'admin')
    @include('admin.partials._sidebar')
    <div class="cs-dash__main"><div class="cs-dash__content">
@else
    <div class="container py-5">
@endif

<div class="msg-inbox-wrap">

    {{-- En-tete --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <p class="text-uppercase small fw-bold mb-1" style="color:#0D9488;letter-spacing:1px;">Communication</p>
            <h1 class="h3 fw-bold mb-0">Messagerie</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('messages.non-lus') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
                <i class="bi bi-envelope-fill me-1"></i> Non lus
            </a>
            @if($role === 'admin')
                <a href="{{ route('admin.messages.nouvelle') }}" class="btn rounded-pill btn-sm text-white fw-semibold"
                   style="background:#0D9488;">
                    <i class="bi bi-pencil-square me-1"></i> Nouveau message
                </a>
            @endif
        </div>
    </div>

    {{-- Barre de recherche --}}
    <form method="GET" action="{{ route('messages.search') }}" class="mb-4">
        <div class="input-group rounded-pill overflow-hidden shadow-sm">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="search" name="q" class="form-control border-start-0 border-end-0"
                   placeholder="Rechercher dans vos messages..." value="{{ request('q') }}">
            <button type="submit" class="btn btn-outline-secondary border-start-0">Rechercher</button>
        </div>
    </form>

    {{-- Liste des conversations --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        @forelse($conversations as $partnerId => $conversation)
            @php
                $first   = $conversation->first();
                $last    = $conversation->last();
                $partner = $first->expediteur_id === auth()->id()
                           ? $first->destinataire
                           : $first->expediteur;

                if (!$partner) continue;

                $unread = $conversation->filter(fn($m) =>
                    $m->destinataire_id === auth()->id() && !$m->lu
                )->count();

                $initiale = strtoupper(substr($partner->prenom ?? $partner->name ?? '?', 0, 1));
                $roleBadgeColor = match($partner->role) {
                    'club'      => '#1A56A0',
                    'joueur'    => '#0D9488',
                    'agent'     => '#7C3AED',
                    'parent'    => '#D97706',
                    'admin'     => '#DC2626',
                    'supporter' => '#6B7280',
                    default     => '#6B7280',
                };
                $roleLabel = match($partner->role) {
                    'club'      => 'Club',
                    'joueur'    => 'Joueur',
                    'agent'     => 'Agent',
                    'parent'    => 'Parent',
                    'admin'     => 'Admin',
                    'supporter' => 'Supporter',
                    default     => ucfirst($partner->role),
                };
            @endphp

            <a href="{{ route('messages.conversation', $partner) }}" class="msg-conv-item d-block text-decoration-none">
                <div class="d-flex align-items-center gap-3">
                    {{-- Avatar --}}
                    @if($partner->avatar)
                        <img src="{{ Storage::url($partner->avatar) }}" alt="{{ $partner->name }}" class="msg-avatar">
                    @else
                        <div class="msg-avatar-placeholder" style="background:linear-gradient(135deg, {{ $roleBadgeColor }}, #0D9488);">
                            {{ $initiale }}
                        </div>
                    @endif

                    {{-- Infos conversation --}}
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-semibold {{ $unread > 0 ? 'text-body' : 'text-body-secondary' }}">
                                {{ $partner->prenom ? $partner->prenom . ' ' . $partner->name : $partner->name }}
                            </span>
                            <span class="badge rounded-pill" style="background:{{ $roleBadgeColor }};font-size:9px;opacity:.85;">
                                {{ $roleLabel }}
                            </span>
                            @if($unread > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:9px;">{{ $unread }}</span>
                            @endif
                        </div>
                        <div class="text-body-secondary text-truncate small {{ $unread > 0 ? 'fw-semibold' : '' }}">
                            @if($last->expediteur_id === auth()->id())
                                <span class="text-muted">Vous : </span>
                            @endif
                            {{ $last->contenu }}
                        </div>
                    </div>

                    {{-- Heure --}}
                    <div class="text-end flex-shrink-0 ms-2">
                        <div class="small text-muted" style="font-size:11px;white-space:nowrap;">
                            {{ $last->created_at->diffForHumans() }}
                        </div>
                        @if($unread > 0)
                            <div class="d-flex justify-content-end mt-1">
                                <div class="msg-unread-dot"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="msg-empty">
                <i class="bi bi-chat-square-text" style="font-size:3rem;opacity:.3;"></i>
                <p class="mt-3 mb-2 fw-semibold">Aucune conversation</p>
                <p class="small text-muted">Vos echanges avec les clubs, joueurs et agents apparaitront ici.</p>
            </div>
        @endforelse
    </div>

    {{-- Lien archives --}}
    <div class="text-center mt-3">
        <a href="{{ route('messages.archives') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-archive me-1"></i> Voir les messages archives
        </a>
    </div>

</div>

@if($role === 'admin')
    </div></div>
@else
    </div>
@endif
@endsection
