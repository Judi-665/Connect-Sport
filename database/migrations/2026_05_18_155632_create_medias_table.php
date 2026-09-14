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
    Schema::create('medias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->foreignId('joueur_id')->nullable()->constrained()->nullOnDelete(); // media lié à un joueur
        $table->foreignId('evenement_id')->nullable()
              ->constrained('evenements_agenda')->nullOnDelete();                   // media lié à un match
        $table->enum('type', ['photo', 'video']);
        $table->string('chemin', 500);                     // chemin storage ou URL CDN
        $table->string('titre', 150)->nullable();
        $table->text('description')->nullable();
        $table->string('miniature', 500)->nullable();      // thumbnail pour les vidéos
        $table->unsignedInteger('taille_ko')->nullable();  // taille fichier en Ko
        $table->unsignedSmallInteger('duree_secondes')->nullable(); // durée si vidéo
        $table->enum('visibilite', ['public', 'club', 'premium'])->default('club');
        $table->tinyInteger('payant')->default(0);
        $table->decimal('prix', 8, 2)->unsigned()->nullable();
        $table->unsignedInteger('vues')->default(0);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['club_id', 'type']);
        $table->index(['club_id', 'visibilite']);
        $table->index('joueur_id');
        $table->index('evenement_id');
        $table->index('payant');
    });
}

public function down(): void
{
    Schema::dropIfExists('medias');
}
};
