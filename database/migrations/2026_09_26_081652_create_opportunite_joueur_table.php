<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunite_joueur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunite_id')->constrained()->cascadeOnDelete();
            $table->foreignId('joueur_id')->constrained()->cascadeOnDelete();
            $table->timestamp('candidature_at')->nullable();
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');
            $table->timestamps();

            $table->unique(['opportunite_id', 'joueur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunite_joueur');
    }
};