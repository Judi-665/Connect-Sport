<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Sport;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubSubscriptionDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_the_active_premium_subscription_instead_of_the_legacy_club_field(): void
    {
        [$user, $club] = $this->createClub(['abonnement' => 'gratuit']);
        $premium = SubscriptionPlan::create([
            'nom' => 'Premium',
            'slug' => 'premium',
            'type_acteur' => 'club',
            'prix' => 10000,
        ]);

        Abonnement::create([
            'club_id' => $club->id,
            'subscription_plan_id' => $premium->id,
            'plan' => 'premium',
            'statut' => 'actif',
            'debut_at' => now()->subDay(),
            'fin_at' => now()->addMonth(),
        ]);

        $this->actingAs($user)
            ->get(route('club.dashboard'))
            ->assertOk()
            ->assertSee('Abonnement Premium actif')
            ->assertDontSee('Passer à Premium');
    }

    public function test_dashboard_uses_plan_slug_when_active_subscription_has_no_plan_relation(): void
    {
        [$user, $club] = $this->createClub(['abonnement' => 'gratuit']);

        Abonnement::create([
            'club_id' => $club->id,
            'subscription_plan_id' => null,
            'plan' => 'premium',
            'statut' => 'actif',
            'debut_at' => now()->subDay(),
            'fin_at' => now()->addMonth(),
        ]);

        $this->actingAs($user)
            ->get(route('club.dashboard'))
            ->assertOk()
            ->assertSee('Abonnement Premium actif')
            ->assertDontSee('Passer à Premium');
    }

    private function createClub(array $attributes = []): array
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'club']);
        $sport = Sport::firstOrCreate(['slug' => 'football'], ['nom' => 'Football']);
        $club = Club::create(array_merge([
            'user_id' => $user->id,
            'sport_id' => $sport->id,
            'nom' => 'Club Abonnement',
            'slug' => 'club-abonnement-' . uniqid(),
            'abonnement' => 'gratuit',
            'actif' => true,
        ], $attributes));

        return [$user, $club];
    }
}