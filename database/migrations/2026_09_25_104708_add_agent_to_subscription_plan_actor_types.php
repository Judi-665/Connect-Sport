<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE subscription_plans MODIFY type_acteur ENUM('club', 'joueur', 'agent') NOT NULL DEFAULT 'club'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::table('subscription_plans')->where('type_acteur', 'agent')->update(['type_acteur' => 'club']);
        DB::statement("ALTER TABLE subscription_plans MODIFY type_acteur ENUM('club', 'joueur') NOT NULL DEFAULT 'club'");
    }
};
