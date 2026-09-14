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
    Schema::create('equipes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->string('nom', 100);
        $table->enum('genre', ['masculin', 'feminin', 'mixte']);
        $table->enum('categorie', ['junior', 'cadet', 'senior', 'veteran', 'autre']);
        $table->text('description')->nullable();
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();

        $table->index(['club_id', 'categorie']);
        $table->index('genre');
    });
}

public function down(): void
{
    Schema::dropIfExists('equipes');
}
};
