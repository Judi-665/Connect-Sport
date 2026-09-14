<?php
// database/seeders/JoueurSubscriptionPlanSeeder.php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class JoueurSubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['nom' => 'Gratuit',  'slug' => 'gratuit',  'type_acteur' => 'joueur', 'prix' => 0,    'frequence' => 'illimite', 'actif' => true],
            ['nom' => 'Standard', 'slug' => 'standard', 'type_acteur' => 'joueur', 'prix' => 1000, 'frequence' => 'mensuel',  'actif' => true],
            ['nom' => 'Premium',  'slug' => 'premium',  'type_acteur' => 'joueur', 'prix' => 2500, 'frequence' => 'mensuel',  'actif' => true],
        ];

        foreach ($plans as $p) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $p['slug'], 'type_acteur' => $p['type_acteur']],
                $p
            );
        }
    }
}