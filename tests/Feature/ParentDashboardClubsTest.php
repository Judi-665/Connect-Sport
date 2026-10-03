<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Joueur;
use App\Models\ParentJoueur;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentDashboardClubsTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_button_links_directly_to_the_only_child_club(): void
    {
        /** @var User $parent */
        $parent = User::factory()->create(['role' => 'parent']);
        $club = $this->createClub('Club Alpha');
        $this->linkChildToParent($parent, $club);

        $this->actingAs($parent)
            ->get(route('parent.dashboard'))
            ->assertOk()
            ->assertSee('Voir le club de mon enfant')
            ->assertSee(route('clubs.show', $club), false)
            ->assertDontSee('Découvrir les clubs');
    }

    public function test_parent_can_choose_between_distinct_clubs_of_multiple_children(): void
    {
        /** @var User $parent */
        $parent = User::factory()->create(['role' => 'parent']);
        $firstClub = $this->createClub('Club Alpha');
        $secondClub = $this->createClub('Club Beta');
        $this->linkChildToParent($parent, $firstClub);
        $this->linkChildToParent($parent, $firstClub);
        $this->linkChildToParent($parent, $secondClub);

        $response = $this->actingAs($parent)->get(route('parent.dashboard'));

        $response->assertOk()
            ->assertSee('Clubs de mes enfants')
            ->assertSee('Club Alpha')
            ->assertSee('Club Beta')
            ->assertSee(route('clubs.show', $firstClub), false)
            ->assertSee(route('clubs.show', $secondClub), false)
            ->assertSee('Explorer tous les clubs');

        $this->assertSame(1, substr_count($response->getContent(), route('clubs.show', $firstClub)));
    }

    public function test_parent_without_child_clubs_keeps_the_discovery_link(): void
    {
        /** @var User $parent */
        $parent = User::factory()->create(['role' => 'parent']);

        $this->actingAs($parent)
            ->get(route('parent.dashboard'))
            ->assertOk()
            ->assertSee('Découvrir les clubs')
            ->assertSee(route('clubs.index'), false);
    }

    private function createClub(string $name): Club
    {
        $sport = Sport::firstOrCreate(['slug' => 'football'], ['nom' => 'Football']);
        $clubUser = User::factory()->create(['role' => 'club']);

        return Club::create([
            'user_id' => $clubUser->id,
            'sport_id' => $sport->id,
            'nom' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)) . '-' . uniqid(),
            'ville' => 'Cotonou',
            'actif' => true,
        ]);
    }

    private function linkChildToParent(User $parent, Club $club): void
    {
        $playerUser = User::factory()->create(['role' => 'joueur']);
        $player = Joueur::create([
            'user_id' => $playerUser->id,
            'club_id' => $club->id,
            'categorie' => 'junior',
            'actif' => true,
        ]);

        ParentJoueur::create([
            'user_id' => $parent->id,
            'joueur_id' => $player->id,
            'lien' => 'pere',
            'acces_stats' => true,
            'acces_agenda' => true,
            'actif' => true,
        ]);
    }
}