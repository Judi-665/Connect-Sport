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
    Schema::create('licences', function (Blueprint $table) {
        $table->id();
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->string('numero_licence', 80)->nullable()->unique();
        $table->string('fichier_pdf', 255);               // chemin storage/licences/
        $table->enum('categorie', ['junior', 'cadet', 'senior', 'veteran']);
        $table->date('date_debut');
        $table->date('date_expiration');
        $table->tinyInteger('active')->default(1);
        $table->text('note')->nullable();                 // observations du club
        $table->timestamps();

        $table->index(['joueur_id', 'active']);
        $table->index(['club_id', 'active']);
        $table->index('date_expiration');                 // pour le cron ExpireLicences
    });
}

public function down(): void
{
    Schema::dropIfExists('licences');
}
};
