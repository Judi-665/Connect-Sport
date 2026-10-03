{{-- resources/views/messages/conversation.blade.php --}}
@php
    $role = auth()->user()->role ?? 'guest';
@endphp
@extends('layouts.app')

@section('title', 'Conversation avec ' . ($user->prenom ? $user->prenom . ' ' . $user->name : $user->name) . ' — Connect Sport')

@if($role === 'agent')
    @section('page-title', 'Conversation')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@push('styles')
<style>
.conv-wrap {
    max-width: 780px;
    margin: 0 auto;
}
.conv-messages-area {
    min-height: 380px;
    max-height: 520px;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
}
.conv-bubble {
    max-width: 72%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: .9rem;
    line-height: 1.5;
    word-break: break-word;
}
.conv-bubble--out {
    background: linear-gradient(135deg, #0D9488, #0D2E5C);
    color: #fff;
    border-bottom-right-radius: 4px;
    align-self: flex-end;
}
.conv-bubble--in {
    background: var(--bs-tertiary-bg, #f0f0f0);
    color: var(--bs-body-color);
    border-bottom-left-radius: 4px;
    align-self: flex-start;
}
.conv-bubble-time {
    font-size: 10px;
    opacity: .65;
    margin-top: 4px;
    display: block;
}
.conv-form-area {
    padding: 16px 20px;
    border-top: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.08));
    background: var(--bs-body-bg);
    border-radius: 0 0 16px 16px;
}
.conv-partner-header {
    display: flex;
    align-items: center;
    gap: 12px;
}
.conv-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}
.conv-avatar-placeholder {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 15px;
    color: #fff;
    flex-shrink: 0;
    background: linear-gradient(135deg, #0D2E5C, #0D9488);
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

<div class="conv-wrap">

    {{-- En-tete conversation --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div class="conv-partner-header">
            {{-- Avatar du partenaire --}}
            @php
                $roleBadgeColor = match($user->role) {
                    'club'      => '#1A56A0',
                    'joueur'    => '#0D9488',
                    'agent'     => '#7C3AED',
                    'parent'    => '#D97706',
                    'admin'     => '#DC2626',
                    'supporter' => '#6B7280',
                    default     => '#6B7280',
                };
                $roleLabel = match($user->role) {
                    'club'      => 'Club',
                    'joueur'    => 'Joueur',
                    'agent'     => 'Agent',
                    'parent'    => 'Parent',
                    'admin'     => 'Admin',
                    'supporter' => 'Supporter',
                    default     => ucfirst($user->role),
                };
                $initiale = strtoupper(substr($user->prenom ?? $user->name ?? '?', 0, 1));
            @endphp

            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="conv-avatar">
            @else
                <div class="conv-avatar-placeholder" style="background:linear-gradient(135deg, {{ $roleBadgeColor }}, #0D9488);">
                    {{ $initiale }}
                </div>
            @endif

            <div>
                <div class="fw-bold fs-5">
                    {{ $user->prenom ? $user->prenom . ' ' . $user->name : $user->name }}
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill" style="background:{{ $roleBadgeColor }};font-size:10px;">
                        {{ $roleLabel }}
                    </span>
                    <span class="text-muted small">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>

    {{-- Zone messages --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="conv-messages-area" id="conv-messages">
            @forelse($messages as $message)
                @php
                    $isMine = $message->expediteur_id === auth()->id();
                @endphp
                <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="conv-bubble {{ $isMine ? 'conv-bubble--out' : 'conv-bubble--in' }}">
                        {{ $message->contenu }}
                        <span class="conv-bubble-time {{ $isMine ? 'text-end' : 'text-start' }}">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                            @if($isMine && $message->lu)
                                <i class="bi bi-check2-all ms-1" title="Lu"></i>
                            @elseif($isMine)
                                <i class="bi bi-check2 ms-1" title="Envoye"></i>
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-chat-square-text" style="font-size:2.5rem;opacity:.3;"></i>
                    <p class="mt-3 small">Demarrez la conversation en envoyant un premier message.</p>
                </div>
            @endforelse
        </div>

        {{-- Zone envoi --}}
        @if(auth()->user()->isSupporter())
            <div class="conv-form-area text-center text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                L'envoi de messages n'est pas disponible pour les supporters.
            </div>
        @else
            <div class="conv-form-area">
                <form method="POST" action="{{ route('messages.send') }}" class="d-flex gap-2 align-items-end">
                    @csrf
                    <input type="hidden" name="destinataire_id" value="{{ $user->id }}">
                    <textarea name="contenu"
                              class="form-control rounded-3 @error('contenu') is-invalid @enderror"
                              rows="2"
                              maxlength="2000"
                              placeholder="Ecrire un message..."
                              required
                              style="resize:none;"></textarea>
                    <button type="submit" class="btn rounded-pill px-3 py-2 flex-shrink-0"
                            style="background:#0D9488;color:#fff;" title="Envoyer">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
                @error('contenu')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        @endif
    </div>

</div>

@if($role === 'admin')
    </div></div>
@else
    </div>
@endif

@push('scripts')
<script>
    // Scroll automatique vers le bas a l'ouverture
    const area = document.getElementById('conv-messages');
    if (area) area.scrollTop = area.scrollHeight;
</script>
@endpush
@endsection
