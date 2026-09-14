<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('agent_joueur', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agent_id')->constrained()->onDelete('cascade');
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
        $table->date('debut_mandat')->nullable();
        $table->date('fin_mandat')->nullable();
        $table->decimal('commission_pourcentage', 5, 2)->nullable(); // % commission agent
        $table->enum('statut', ['en_attente', 'actif', 'termine', 'resilie'])->default('en_attente');
        $table->text('note')->nullable();                  // conditions particulières
        $table->timestamps();

        $table->unique(['agent_id', 'joueur_id']);         // un agent représente un joueur une seule fois
        $table->index(['joueur_id', 'statut']);
        $table->index(['agent_id', 'statut']);
        $table->index('fin_mandat');                       // pour détecter les mandats expirés
    });
}

public function down(): void
{
    Schema::dropIfExists('agent_joueur');
}
};
