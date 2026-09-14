<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Club;
use App\Models\Joueur;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentTransferWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_submit_an_offer_for_an_active_mandate(): void
    {
        /** @var User $agentUser */
        $agentUser = User::factory()->create(['role' => 'agent']);
        $agent = Agent::create(['user_id' => $agentUser->id, 'numero_accreditation' => 'AG-001', 'actif' => true]);
        $sport = Sport::create(['nom' => 'Football', 'slug' => 'football']);
        $source = $this->club($sport, 'Source FC', 1);
        $destination = $this->club($sport, 'Destination FC', 2);
        $playerUser = User::factory()->create(['role' => 'joueur']);
        $player = Joueur::create(['user_id' => $playerUser->id, 'club_id' => $source->id, 'categorie' => 'senior', 'actif' => true]);
        $agent->joueurs()->attach($player->id, ['statut' => 'actif']);

        $response = $this->actingAs($agentUser)->post(route('agent.transferts.store'), [
            'joueur_id' => $player->id,
            'club_destinataire_id' => $destination->id,
            'type' => 'definitif',
            'devise' => 'XOF',
            'montant' => 100000,
            'agent_note' => 'Offre de test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transferts', ['agent_id' => $agent->id, 'joueur_id' => $player->id, 'club_destinataire_id' => $destination->id, 'statut' => 'en_attente']);
        $this->assertDatabaseHas('transfert_histories', ['nouveau_statut' => 'en_attente', 'note' => 'Offre de test']);
        $this->assertDatabaseHas('agent_club', ['agent_id' => $agent->id, 'club_id' => $destination->id]);
    }

    public function test_agent_cannot_update_a_transfer_that_does_not_belong_to_them(): void
    {
        /** @var User $agentUser */
        $agentUser = User::factory()->create(['role' => 'agent']);
        Agent::create(['user_id' => $agentUser->id, 'numero_accreditation' => 'AG-002', 'actif' => true]);

        $response = $this->actingAs($agentUser)->post('/agent/transferts/999/negocier', [
            'statut' => 'accepte',
        ]);

        $response->assertNotFound();
    }

    private function club(Sport $sport, string $name, int $userId): Club
    {
        $user = User::factory()->create(['role' => 'club', 'email' => "club{$userId}@example.com"]);
        return Club::create([
            'user_id' => $user->id,
            'sport_id' => $sport->id,
            'nom' => $name,
            'slug' => strtolower(str_replace(' ', '-', $name)),
            'actif' => true,
        ]);
    }
}
