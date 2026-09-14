<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('statistiques', function (Blueprint $table) {
        $table->id();
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->foreignId('evenement_id')->nullable()
              ->constrained('evenements_agenda')->nullOnDelete(); // match concerné
        $table->string('saison', 10);                      // ex : "2024-2025"

        // Stats communes à tous les sports
        $table->unsignedSmallInteger('matchs_joues')->default(0);
        $table->unsignedSmallInteger('matchs_titulaire')->default(0);
        $table->unsignedSmallInteger('matchs_remplacant')->default(0);
        $table->unsignedSmallInteger('minutes_jouees')->default(0);
        $table->unsignedSmallInteger('buts')->default(0);
        $table->unsignedSmallInteger('passes_decisives')->default(0);
        $table->unsignedTinyInteger('cartons_jaunes')->default(0);
        $table->unsignedTinyInteger('cartons_rouges')->default(0);
        $table->decimal('note_moyenne', 4, 2)->nullable();  // note /10

        // Stats complémentaires selon le sport (JSON flexible)
        $table->json('stats_complementaires')->nullable();
        // Exemples selon sport :
        // Football  → {"tirs_cadres": 12, "duels_gagnes": 45}
        // Handball  → {"arrets": 28, "penaltys_arretes": 3}
        // Basketball→ {"rebonds": 34, "interceptions": 8}

        $table->tinyInteger('valide')->default(0);         // validé par le coach/club
        $table->timestamp('valide_at')->nullable();
        $table->timestamps();

        $table->unique(['joueur_id', 'evenement_id']);     // une stat par joueur par match
        $table->index(['joueur_id', 'saison']);
        $table->index(['club_id', 'saison']);
        $table->index('valide');
    });
}

public function down(): void
{
    Schema::dropIfExists('statistiques');
}
};
