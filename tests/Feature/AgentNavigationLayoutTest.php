<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentNavigationLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_keeps_the_dashboard_sidebar_on_public_navigation_pages(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['role' => 'agent']);

        foreach (['home', 'clubs.index', 'joueurs.sans-club', 'opportunites.index'] as $routeName) {
            $response = $this->actingAs($agent)->get(route($routeName));

            $response->assertOk()
                ->assertSee('cs-sidebar', false)
                ->assertSee(route('agent.dashboard'), false)
                ->assertSee('Commissions')
                ->assertDontSee('agentSidebar', false);
        }
    }

    public function test_public_club_page_keeps_the_public_layout_for_non_agents(): void
    {
        /** @var User $visitor */
        $visitor = User::factory()->create(['role' => 'supporter']);

        $response = $this->actingAs($visitor)->get(route('clubs.index'));

        $response->assertOk()
            ->assertDontSee('cs-sidebar', false);
    }
}