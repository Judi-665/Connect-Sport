<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('abonnements', function (Blueprint $table) {
        $table->foreignId('club_id')->nullable()->change(); // nécessite doctrine/dbal
        $table->foreignId('joueur_id')->nullable()->after('club_id')
              ->constrained('joueurs')->nullOnDelete();
        $table->index(['joueur_id', 'statut']);
    });

    Schema::table('subscription_plans', function (Blueprint $table) {
        $table->enum('type_acteur', ['club', 'joueur'])->default('club')->after('slug');
    });

    // Les plans existants sont tous des plans club
    DB::table('subscription_plans')->update(['type_acteur' => 'club']);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {
            //
        });
    }
};
