<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('poste_propose', 60)->nullable();
            $table->text('message')->nullable();              // motivation du joueur
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee', 'annulee'])
                  ->default('en_attente');
            $table->text('note_club')->nullable();             // réponse/motif du club
            $table->foreignId('repondu_par')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('repondu_at')->nullable();
            $table->timestamps();

            $table->index(['joueur_id', 'statut']);
            $table->index(['club_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};