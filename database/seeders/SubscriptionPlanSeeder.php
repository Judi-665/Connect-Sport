<?php
// database/seeders/SubscriptionPlanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('subscription_plans')->insert([
            [
                'nom'                  => 'Gratuit',
                'slug'                 => 'gratuit',
                'prix'                 => 0,
                'frequence'            => 'illimite',
                'max_equipes'          => 1,
                'multi_equipes'        => false,
                'gestion_licences'     => false,
                'agenda'               => false,
                'stats_avancees'       => false,
                'stockage_etendu'      => false,
                'mise_en_avant'        => false,
                'outils_marketing'     => false,
                'galerie_media'        => false,
                'notifications_ciblees'=> false,
                'actif'                => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'nom'                  => 'Standard',
                'slug'                 => 'standard',
                'prix'                 => 5000,
                'frequence'            => 'mensuel',
                'max_equipes'          => -1,      // illimité
                'multi_equipes'        => true,
                'gestion_licences'     => true,
                'agenda'               => true,
                'stats_avancees'       => false,
                'stockage_etendu'      => false,
                'mise_en_avant'        => false,
                'outils_marketing'     => false,
                'galerie_media'        => false,
                'notifications_ciblees'=> false,
                'actif'                => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'nom'                  => 'Premium',
                'slug'                 => 'premium',
                'prix'                 => 25000,
                'frequence'            => 'annuel',
                'max_equipes'          => -1,      // illimité
                'multi_equipes'        => true,
                'gestion_licences'     => true,
                'agenda'               => true,
                'stats_avancees'       => true,
                'stockage_etendu'      => true,
                'mise_en_avant'        => true,
                'outils_marketing'     => true,
                'galerie_media'        => true,
                'notifications_ciblees'=> true,
                'actif'                => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);
    }
}