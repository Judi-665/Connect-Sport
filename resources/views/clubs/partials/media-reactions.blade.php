@php($selectedReaction = $media->reactions->first()?->type)

<div class="d-flex align-items-center gap-2 mt-3" aria-label="Réactions à cette publication" data-media-reactions>
    @if($media->visibilite === 'public')
    @auth
        <form method="POST" action="{{ route('medias.reactions.toggle', $media) }}" data-reaction-form>
            @csrf
            <input type="hidden" name="reaction" value="like">
            <button type="submit"
                    class="btn btn-sm {{ $selectedReaction === 'like' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill"
                    data-reaction-button="like"
                    aria-pressed="{{ $selectedReaction === 'like' ? 'true' : 'false' }}">
                <i class="bi bi-hand-thumbs-up{{ $selectedReaction === 'like' ? '-fill' : '' }} me-1" data-reaction-icon="like"></i>
                J’aime <span class="ms-1" data-reaction-count="like">{{ $media->likes_count ?? 0 }}</span>
            </button>
        </form>
        <form method="POST" action="{{ route('medias.reactions.toggle', $media) }}" data-reaction-form>
            @csrf
            <input type="hidden" name="reaction" value="love">
            <button type="submit"
                    class="btn btn-sm {{ $selectedReaction === 'love' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill"
                    data-reaction-button="love"
                    aria-pressed="{{ $selectedReaction === 'love' ? 'true' : 'false' }}">
                <i class="bi bi-heart{{ $selectedReaction === 'love' ? '-fill' : '' }} me-1" data-reaction-icon="love"></i>
                J’adore <span class="ms-1" data-reaction-count="love">{{ $media->loves_count ?? 0 }}</span>
            </button>
        </form>
        <span class="visually-hidden" role="status" aria-live="polite" data-reaction-status></span>
    @else
        <span class="btn btn-sm btn-outline-primary rounded-pill disabled" aria-label="J’aime {{ $media->likes_count ?? 0 }} fois">
            <i class="bi bi-hand-thumbs-up me-1"></i>J’aime {{ $media->likes_count ?? 0 }}
        </span>
        <span class="btn btn-sm btn-outline-danger rounded-pill disabled" aria-label="J’adore {{ $media->loves_count ?? 0 }} fois">
            <i class="bi bi-heart me-1"></i>J’adore {{ $media->loves_count ?? 0 }}
        </span>
        <a href="{{ route('login') }}" class="small ms-auto">Connectez-vous pour réagir</a>
    @endauth
    @else
        <span class="small text-body-secondary"><i class="bi bi-hand-thumbs-up me-1"></i>J’aime {{ $media->likes_count ?? 0 }}</span>
        <span class="small text-body-secondary"><i class="bi bi-heart me-1"></i>J’adore {{ $media->loves_count ?? 0 }}</span>
    @endif
</div>

@auth
    @if($media->visibilite === 'public')
        @once
            @push('scripts')
                <script>
                    document.addEventListener('submit', async (event) => {
                        const form = event.target.closest('form[data-reaction-form]');
                        if (!form) return;

                        event.preventDefault();
                        const group = form.closest('[data-media-reactions]');
                        const status = group.querySelector('[data-reaction-status]');
                        const buttons = group.querySelectorAll('[data-reaction-button]');
                        buttons.forEach(button => button.disabled = true);
                        status.textContent = '';

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: new FormData(form),
                                credentials: 'same-origin',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const result = await response.json();
                            if (!response.ok) throw new Error(result.message || 'La réaction n’a pas pu être enregistrée.');

                            group.querySelectorAll('[data-reaction-button]').forEach(button => {
                                const type = button.dataset.reactionButton;
                                const active = result.reaction === type;
                                button.setAttribute('aria-pressed', active ? 'true' : 'false');
                                button.classList.remove('btn-primary', 'btn-outline-primary', 'btn-danger', 'btn-outline-danger');
                                button.classList.add(type === 'like'
                                    ? (active ? 'btn-primary' : 'btn-outline-primary')
                                    : (active ? 'btn-danger' : 'btn-outline-danger'));

                                const icon = button.querySelector('[data-reaction-icon]');
                                icon.classList.toggle(type === 'like' ? 'bi-hand-thumbs-up-fill' : 'bi-heart-fill', active);
                                icon.classList.toggle(type === 'like' ? 'bi-hand-thumbs-up' : 'bi-heart', !active);
                            });

                            group.querySelector('[data-reaction-count="like"]').textContent = result.likes_count;
                            group.querySelector('[data-reaction-count="love"]').textContent = result.loves_count;
                            status.textContent = result.reaction ? 'Réaction enregistrée.' : 'Réaction retirée.';
                        } catch (error) {
                            status.textContent = error.message || 'Erreur réseau. Réessayez.';
                        } finally {
                            buttons.forEach(button => button.disabled = false);
                        }
                    });
                </script>
            @endpush
        @endonce
    @endif
@endauth