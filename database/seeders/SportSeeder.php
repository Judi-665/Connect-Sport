<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportSeeder extends Seeder
{
    public function run(): void
    {
      DB::table('sports')->insert([
    ['nom' => 'Football',   'slug' => 'football',   'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Handball',   'slug' => 'handball',   'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Basketball', 'slug' => 'basketball', 'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Volleyball', 'slug' => 'volleyball', 'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Athlétisme','slug' => 'athletisme', 'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Natation',   'slug' => 'natation',   'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Rugby',      'slug' => 'rugby',      'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Tennis',     'slug' => 'tennis',     'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Boxe',       'slug' => 'boxe',       'created_at' => now(), 'updated_at' => now()],
    ['nom' => 'Autre',      'slug' => 'autre',      'created_at' => now(), 'updated_at' => now()],
]);
    }
}