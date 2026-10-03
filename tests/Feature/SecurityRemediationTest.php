<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Joueur;
use App\Models\Media;
use App\Models\ParentJoueur;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_registration_is_not_public(): void
    {
        $this->get('/admin/inscription')->assertNotFound();
        $this->post('/admin/inscription', [
            'name' => 'Injected Admin',
            'email' => 'injected-admin@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'injected-admin@example.test']);
    }

    public function test_admin_login_is_available_from_the_public_footer(): void
    {
        $this->get(route('admin.login'))->assertOk();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('admin.login'), false)
            ->assertSee('Espace administration');
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/connexion', [
                'email' => 'throttle-test@example.test',
                'password' => 'incorrect-password',
            ])->assertRedirect();
        }

        $this->post('/connexion', [
            'email' => 'throttle-test@example.test',
            'password' => 'incorrect-password',
        ])->assertTooManyRequests();
    }

    public function test_player_parent_link_requires_player_consent(): void
    {
        /** @var User $parent */
        $parent = User::factory()->create(['role' => 'parent']);
        /** @var User $playerUser */
        $playerUser = User::factory()->create(['role' => 'joueur']);
        $player = Joueur::create([
            'user_id' => $playerUser->id,
            'categorie' => 'junior',
            'actif' => true,
        ]);

        $this->actingAs($parent)->post(route('parent.confirm-joueur'), [
            'joueur_id' => $player->id,
            'lien' => 'tuteur',
        ])->assertRedirect(route('parent.dashboard'));

        $this->assertDatabaseHas('parents', [
            'user_id' => $parent->id,
            'joueur_id' => $player->id,
            'actif' => false,
            'acces_stats' => false,
            'acces_agenda' => false,
        ]);

        $link = ParentJoueur::where('user_id', $parent->id)->where('joueur_id', $player->id)->firstOrFail();
        $this->actingAs($playerUser)->patch(route('joueur.parents.accepter', $link))
            ->assertRedirect();

        $this->assertDatabaseHas('parents', [
            'id' => $link->id,
            'actif' => true,
            'acces_stats' => true,
            'acces_agenda' => true,
        ]);
    }

    public function test_club_cannot_download_another_clubs_licence(): void
    {
        $sport = Sport::create(['nom' => 'Handball', 'slug' => 'handball']);
        /** @var User $clubAUser */
        $clubAUser = User::factory()->create(['role' => 'club']);
        /** @var User $clubBUser */
        $clubBUser = User::factory()->create(['role' => 'club']);
        $clubA = $this->club($clubAUser, $sport, 'Alpha');
        $clubB = $this->club($clubBUser, $sport, 'Beta');
        /** @var User $playerUser */
        $playerUser = User::factory()->create(['role' => 'joueur']);
        $player = Joueur::create([
            'user_id' => $playerUser->id,
            'club_id' => $clubA->id,
            'categorie' => 'senior',
            'actif' => true,
        ]);
        $licence = \App\Models\Licence::create([
            'joueur_id' => $player->id,
            'club_id' => $clubA->id,
            'fichier_pdf' => "licences/{$clubA->id}/license.pdf",
            'categorie' => 'senior',
            'date_debut' => today(),
            'date_expiration' => today()->addYear(),
            'active' => true,
        ]);

        $this->actingAs($clubBUser)->get(route('club.licences.download', $licence))
            ->assertForbidden();
    }

    public function test_player_profile_cannot_self_assign_a_club_or_team(): void
    {
        $sport = Sport::create(['nom' => 'Basketball', 'slug' => 'basketball']);
        /** @var User $clubUser */
        $clubUser = User::factory()->create(['role' => 'club']);
        $club = $this->club($clubUser, $sport, 'Ravens');
        /** @var User $playerUser */
        $playerUser = User::factory()->create(['role' => 'joueur']);
        $team = $club->equipes()->create([
            'nom' => 'Equipe A',
            'genre' => 'mixte',
            'categorie' => 'senior',
            'actif' => true,
        ]);

        $this->actingAs($playerUser)->post(route('joueur.profil.store'), [
            'categorie' => 'senior',
            'club_id' => $club->id,
            'equipe_id' => $team->id,
        ])->assertRedirect(route('joueur.dashboard'));

        $this->assertDatabaseHas('joueurs', [
            'user_id' => $playerUser->id,
            'club_id' => null,
            'equipe_id' => null,
        ]);
    }

    public function test_private_club_media_cannot_be_fetched_by_a_guest(): void
    {
        Storage::fake('media_private');
        $sport = Sport::create(['nom' => 'Volley', 'slug' => 'volley']);
        /** @var User $clubUser */
        $clubUser = User::factory()->create(['role' => 'club']);
        $club = $this->club($clubUser, $sport, 'Spikers');
        $path = "medias/{$club->id}/private.png";
        Storage::disk('media_private')->put($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p1sAAAAASUVORK5CYII='));
        $media = Media::create([
            'club_id' => $club->id,
            'type' => 'photo',
            'chemin' => $path,
            'visibilite' => 'club',
            'payant' => false,
        ]);

        $this->get(route('medias.file', $media))->assertNotFound();
    }

    private function club(User $user, Sport $sport, string $name): Club
    {
        return Club::create([
            'user_id' => $user->id,
            'sport_id' => $sport->id,
            'nom' => $name,
            'slug' => strtolower($name),
            'actif' => true,
        ]);
    }
}