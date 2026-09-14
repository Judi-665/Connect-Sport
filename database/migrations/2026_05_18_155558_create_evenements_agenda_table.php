<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('evenements_agenda', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->string('titre', 150);
        $table->enum('type', ['match', 'entrainement', 'evenement']);
        $table->text('description')->nullable();
        $table->string('lieu', 150)->nullable();
        $table->dateTime('debut_at');
        $table->dateTime('fin_at')->nullable();

        // Infos adversaire (si type = match)
        $table->string('adversaire_nom', 120)->nullable();
        $table->string('adversaire_logo', 255)->nullable();
        $table->enum('domicile_exterieur', ['domicile', 'exterieur', 'neutre'])->nullable();

        // Résultat (rempli après le match)
        $table->unsignedTinyInteger('score_nous')->nullable();
        $table->unsignedTinyInteger('score_eux')->nullable();
        $table->enum('resultat', ['victoire', 'defaite', 'nul'])->nullable();

        // Visibilité
        $table->enum('visibilite', ['public', 'club', 'equipe'])->default('club');
        $table->tinyInteger('convocation_envoyee')->default(0);

        $table->timestamps();
        $table->softDeletes();

        $table->index(['club_id', 'debut_at']);
        $table->index('type');
        $table->index('resultat');
    });
}

public function down(): void
{
    Schema::dropIfExists('evenements_agenda');
}
};
