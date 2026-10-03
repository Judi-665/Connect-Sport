<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipe_joueur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipe_id')->constrained()->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();               // null = affectation active
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index(['joueur_id', 'actif']);
            $table->index(['equipe_id', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipe_joueur');
    }
};