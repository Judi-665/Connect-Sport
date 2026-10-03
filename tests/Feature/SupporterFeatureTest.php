<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Club;
use App\Models\Sport;
use App\Models\Supporter;
use App\Models\EvenementAgenda;
use App\Models\Media;
use App\Models\Opportunite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupporterFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createClub(string $nom, string $slug): Club
    {
        $sport = Sport::firstOrCreate(['nom' => 'Football'], ['slug' => 'football']);
        $clubUser = User::factory()->create(['role' => 'club']);
        return Club::create([
            'user_id' => $clubUser->id,
            'sport_id' => $sport->id,
            'nom' => $nom,
            'slug' => $slug,
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
            'actif' => 1,
        ]);
    }

    public function test_supporter_can_access_dashboard_and_auto_creates_supporter_profile(): void
    {
        $user = User::factory()->create(['role' => 'supporter']);

        $response = $this->actingAs($user)->get(route('supporter.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Espace Supporter');
        $this->assertDatabaseHas('supporters', ['user_id' => $user->id]);
    }

    public function test_supporter_can_follow_club_gratuitement(): void
    {
        $user = User::factory()->create(['role' => 'supporter']);
        $club = $this->createClub('Club Test Follow', 'club-test-follow');

        $response = $this->actingAs($user)->post(route('supporter.club.suivre', $club));

        $response->assertRedirect();
        $supporter = $user->fresh()->supporter;
        $this->assertTrue($supporter->isFollowing($club));
        $this->assertTrue($supporter->hasNotificationsActive($club));
    }

    public function test_supporter_can_toggle_notifications(): void
    {
        $user = User::factory()->create(['role' => 'supporter']);
        $club = $this->createClub('Club Test Toggle Notif', 'club-test-toggle-notif');

        $supporter = Supporter::create(['user_id' => $user->id, 'actif' => 1]);
        $supporter->clubs()->attach($club->id, [
            'type_abonnement' => 'gratuit',
            'notifications_actives' => 1,
        ]);

        // Toggle to off
        $response = $this->actingAs($user)->postJson(route('supporter.club.notifications.toggle', $club));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'notifications_actives' => false,
        ]);
        $this->assertFalse($supporter->hasNotificationsActive($club));

        // Toggle back to on
        $response2 = $this->actingAs($user)->postJson(route('supporter.club.notifications.toggle', $club));
        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'notifications_actives' => true,
        ]);
        $this->assertTrue($supporter->hasNotificationsActive($club));
    }

    public function test_supporter_can_unfollow_club(): void
    {
        $user = User::factory()->create(['role' => 'supporter']);
        $club = $this->createClub('Club Test Unfollow', 'club-test-unfollow');

        $supporter = Supporter::create(['user_id' => $user->id, 'actif' => 1]);
        $supporter->clubs()->attach($club->id, [
            'type_abonnement' => 'gratuit',
            'notifications_actives' => 1,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('supporter.club.quitter', $club));
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_following' => false]);
        $this->assertFalse($supporter->isFollowing($club));
    }

    public function test_supporter_feed_displays_upcoming_matches_results_and_media(): void
    {
        $user = User::factory()->create(['role' => 'supporter']);
        $club = $this->createClub('Lions de Cotonou', 'lions-de-cotonou');

        $supporter = Supporter::create(['user_id' => $user->id, 'actif' => 1]);
        $supporter->clubs()->attach($club->id, ['type_abonnement' => 'gratuit', 'notifications_actives' => 1]);

        // Prochain match
        EvenementAgenda::create([
            'club_id' => $club->id,
            'titre' => 'Derby contre Requins',
            'type' => 'match',
            'lieu' => 'Stade Général Mathieu Kérékou',
            'debut_at' => now()->addDays(2),
            'adversaire_nom' => 'Requins FC',
            'domicile_exterieur' => 'domicile',
            'visibilite' => 'public',
        ]);

        // Dernier résultat
        EvenementAgenda::create([
            'club_id' => $club->id,
            'titre' => 'Choc face aux Panthères',
            'type' => 'match',
            'lieu' => 'Stade René Pleven',
            'debut_at' => now()->subDays(3),
            'adversaire_nom' => 'Panthères',
            'domicile_exterieur' => 'exterieur',
            'score_nous' => 3,
            'score_eux' => 1,
            'resultat' => 'victoire',
            'visibilite' => 'public',
        ]);

        // Média
        Media::create([
            'club_id' => $club->id,
            'type' => 'photo',
            'chemin' => 'medias/test-photo.jpg',
            'titre' => 'Entraînement veille de derby',
            'visibilite' => 'public',
        ]);

        $response = $this->actingAs($user)->get(route('supporter.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Derby contre Requins');
        $response->assertSee('Requins FC');
        $response->assertSee('Choc face aux Panthères');
        $response->assertSee('3 - 1');
        $response->assertSee('Victoire');
        $response->assertSee('Entraînement veille de derby');
    }

    public function test_supporter_can_access_mes_clubs_and_decouvrir_and_profil(): void
    {
        $user = User::factory()->create(['role' => 'supporter']);
        $club = $this->createClub('Dragons de l\'Ouémé', 'dragons-oueme');

        $supporter = Supporter::create(['user_id' => $user->id, 'actif' => 1]);
        $supporter->clubs()->attach($club->id, ['type_abonnement' => 'gratuit', 'notifications_actives' => 1]);

        // Page mes clubs
        $responseClubs = $this->actingAs($user)->get(route('supporter.clubs'));
        $responseClubs->assertStatus(200);
        $responseClubs->assertSee('Dragons de l\'Ouémé');

        // Page découvrir
        $responseDecouvrir = $this->actingAs($user)->get(route('supporter.decouvrir'));
        $responseDecouvrir->assertStatus(200);
        $responseDecouvrir->assertSee('Explorer et suivre des clubs');

        // Page profil
        $responseProfil = $this->actingAs($user)->get(route('supporter.profile'));
        $responseProfil->assertStatus(200);
        $responseProfil->assertSee('Mon Profil Supporter');
    }

    public function test_supporter_cannot_access_opportunities_or_send_messages(): void
    {
        $supporter = User::factory()->create(['role' => 'supporter']);
        $clubUser = User::factory()->create(['role' => 'club']);
        $opportunite = Opportunite::create([
            'user_id' => $clubUser->id,
            'titre' => 'Recrutement test',
            'type' => 'offre_club',
            'description' => 'Description de test',
        ]);

        $this->actingAs($supporter)
            ->get(route('opportunites.index'))
            ->assertForbidden();

        $this->get(route('opportunites.show', $opportunite))
            ->assertForbidden();

        $this->post(route('messages.send'), [
            'destinataire_id' => $clubUser->id,
            'contenu' => 'Bonjour',
        ])->assertForbidden();

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_supporter_sees_opportunities_hidden_and_conversation_read_only(): void
    {
        $supporter = User::factory()->create(['role' => 'supporter']);
        $otherUser = User::factory()->create(['role' => 'club']);

        $this->actingAs($supporter)
            ->get(route('supporter.dashboard'))
            ->assertOk()
            ->assertDontSee(route('opportunites.index'));

        $this->get(route('messages.conversation', $otherUser))
            ->assertOk()
            ->assertSee('L’envoi de messages n’est pas disponible pour les supporters.')
            ->assertDontSee(route('messages.send'));
    }
}
