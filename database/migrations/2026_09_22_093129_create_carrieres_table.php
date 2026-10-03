<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('poste', 60)->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();              

           
          
            $table->string('origine', 20);                     // candidature, transfert, formation
            $table->unsignedBigInteger('origine_id')->nullable();

            $table->timestamps();

            $table->index(['joueur_id', 'date_debut']);
            $table->index(['club_id', 'date_debut']);
            $table->index(['origine', 'origine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrieres');
    }
};