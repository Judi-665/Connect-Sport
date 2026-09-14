<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('difficultes_voeux', function (Blueprint $table) {
        $table->id();
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
        $table->enum('type', ['difficulte', 'voeu']);
        $table->enum('categorie', [
            'technique',
            'physique',
            'tactique',
            'mental',
            'financier',
            'administratif',
            'autre'
        ]);
        $table->string('titre', 150);
        $table->text('contenu');
        $table->enum('priorite', ['faible', 'moyenne', 'haute'])->default('moyenne');
        $table->tinyInteger('resolu')->default(0);
        $table->timestamp('resolu_at')->nullable();        // date de résolution
        $table->text('note_resolution')->nullable();       // comment ça a été résolu
        $table->tinyInteger('visible_club')->default(0);   // partagé avec le club
        $table->tinyInteger('visible_agent')->default(0);  // partagé avec l'agent
        $table->timestamps();
        $table->softDeletes();

        $table->index(['joueur_id', 'type']);
        $table->index(['joueur_id', 'resolu']);
        $table->index(['type', 'categorie']);
        $table->index('priorite');
    });
}

public function down(): void
{
    Schema::dropIfExists('difficultes_voeux');
}
};
