<?php
// database/seeders/AgentSubscriptionPlansSeeder.php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class AgentSubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
    ['nom' => 'Gratuit',          'slug' => 'gratuit',          'frequence' => 'illimite', 'prix' => 0,
        'max_joueurs_representes' => 3, 'stats_avancees' => false, 'mise_en_avant' => false, 'notifications_ciblees' => false],
    ['nom' => 'Standard mensuel', 'slug' => 'standard-mensuel', 'frequence' => 'mensuel',  'prix' => 5000,
        'max_joueurs_representes' => null, 'stats_avancees' => false, 'mise_en_avant' => true, 'notifications_ciblees' => false],
    ['nom' => 'Standard annuel',  'slug' => 'standard-annuel',  'frequence' => 'annuel',   'prix' => 50000,
        'max_joueurs_representes' => null, 'stats_avancees' => false, 'mise_en_avant' => true, 'notifications_ciblees' => false],
    ['nom' => 'Premium mensuel',  'slug' => 'premium-mensuel',  'frequence' => 'mensuel',  'prix' => 10000,
        'max_joueurs_representes' => null, 'stats_avancees' => true, 'mise_en_avant' => true, 'notifications_ciblees' => true],
    ['nom' => 'Premium annuel',   'slug' => 'premium-annuel',   'frequence' => 'annuel',   'prix' => 100000,
        'max_joueurs_representes' => null, 'stats_avancees' => true, 'mise_en_avant' => true, 'notifications_ciblees' => true],
];

foreach ($plans as $plan) {
    SubscriptionPlan::updateOrCreate(
        ['slug' => $plan['slug'], 'type_acteur' => 'agent'],
        array_merge($plan, ['type_acteur' => 'agent', 'actif' => true])
    );
}
    }
}