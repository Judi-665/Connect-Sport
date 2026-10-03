<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sponsors')
            ->whereIn('type_visibilite', ['maillot_domicile', 'maillot_exterieur', 'pancarte', 'digital'])
            ->update([
                'type_visibilite' => DB::raw("CASE
                    WHEN type_visibilite IN ('maillot_domicile', 'maillot_exterieur', 'digital') THEN 'logo'
                    WHEN type_visibilite = 'pancarte' THEN 'banniere'
                    ELSE type_visibilite
                END"),
            ]);

        Schema::table('sponsors', function (Blueprint $table) {
            $table->enum('type_visibilite', ['logo', 'banniere', 'tous'])
                ->default('logo')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('sponsors')
            ->whereIn('type_visibilite', ['logo', 'banniere'])
            ->update([
                'type_visibilite' => DB::raw("CASE
                    WHEN type_visibilite = 'banniere' THEN 'pancarte'
                    WHEN type_visibilite = 'logo' THEN 'digital'
                    ELSE type_visibilite
                END"),
            ]);

        Schema::table('sponsors', function (Blueprint $table) {
            $table->enum('type_visibilite', [
                'maillot_domicile',
                'maillot_exterieur',
                'pancarte',
                'digital',
                'tous',
            ])->default('digital')->change();
        });
    }
};