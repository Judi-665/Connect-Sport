<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Media;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaReactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_like_and_love_public_media_and_toggle_it_off(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'supporter']);
        $media = $this->createMedia();

        $this->actingAs($user)
            ->postJson(route('medias.reactions.toggle', $media), ['reaction' => 'like'])
            ->assertOk()
            ->assertJson(['reaction' => 'like', 'likes_count' => 1, 'loves_count' => 0]);

        $this->assertDatabaseHas('media_reactions', [
            'media_id' => $media->id,
            'user_id' => $user->id,
            'type' => 'like',
        ]);

        $this->postJson(route('medias.reactions.toggle', $media), ['reaction' => 'love'])
            ->assertOk()
            ->assertJson(['reaction' => 'love', 'likes_count' => 0, 'loves_count' => 1]);

        $this->postJson(route('medias.reactions.toggle', $media), ['reaction' => 'love'])
            ->assertOk()
            ->assertJson(['reaction' => null, 'likes_count' => 0, 'loves_count' => 0]);

        $this->assertDatabaseMissing('media_reactions', [
            'media_id' => $media->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_guests_cannot_react_and_private_media_cannot_receive_public_reactions(): void
    {
        $media = $this->createMedia();

        $this->post(route('medias.reactions.toggle', $media), ['reaction' => 'like'])
            ->assertRedirect(route('login'));

        /** @var User $user */
        $user = User::factory()->create(['role' => 'supporter']);
        $privateMedia = $this->createMedia(['visibilite' => 'club']);

        $this->actingAs($user)
            ->postJson(route('medias.reactions.toggle', $privateMedia), ['reaction' => 'like'])
            ->assertNotFound();

        $this->assertDatabaseCount('media_reactions', 0);
    }

    public function test_public_club_gallery_shows_counts_and_reaction_controls(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'supporter']);
        $media = $this->createMedia(['titre' => 'Photo du match']);
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create(['role' => 'supporter']);
        $media->reactions()->create(['user_id' => $anotherUser->id, 'type' => 'love']);

        $this->get(route('clubs.medias', $media->club))
            ->assertOk()
            ->assertSee('Photo du match')
            ->assertSee('J’adore')
            ->assertSee('Connectez-vous pour réagir');

        $this->actingAs($user)
            ->get(route('clubs.medias', $media->club))
            ->assertOk()
            ->assertSee('Photo du match')
            ->assertSee('J’adore')
            ->assertDontSee('Connectez-vous pour réagir')
            ->assertSee('name="reaction"', false)
            ->assertSee('data-reaction-form', false)
            ->assertSee('event.preventDefault()', false)
            ->assertSee('fetch(form.action', false);
    }

    public function test_public_video_gallery_renders_an_inline_player_for_players_and_visitors(): void
    {
        $video = $this->createMedia([
            'type' => 'video',
            'chemin' => 'medias/public/match.mp4',
            'titre' => 'Résumé du match',
        ]);

        $this->get(route('clubs.medias', $video->club))
            ->assertOk()
            ->assertSee('Résumé du match')
            ->assertSee('<video', false)
            ->assertSee('controls', false)
            ->assertSee('playsinline', false)
            ->assertSee(route('medias.file', $video), false);
    }

    private function createMedia(array $mediaAttributes = []): Media
    {
        $sport = Sport::firstOrCreate(['slug' => 'football'], ['nom' => 'Football']);
        $clubUser = User::factory()->create(['role' => 'club']);
        $club = Club::create([
            'user_id' => $clubUser->id,
            'sport_id' => $sport->id,
            'nom' => 'Club Réactions',
            'slug' => 'club-reactions-' . uniqid(),
            'actif' => true,
        ]);

        return Media::create(array_merge([
            'club_id' => $club->id,
            'type' => 'photo',
            'chemin' => 'medias/public/photo.jpg',
            'visibilite' => 'public',
            'payant' => false,
        ], $mediaAttributes));
    }
}