<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('opportunites', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete(); // auteur : club ou admin
        $table->foreignId('user_id')->constrained()->onDelete('cascade');        // créateur (admin si club_id null)
        $table->string('titre', 150);
        $table->enum('type', [
            'offre_club',
            'stage',
            'formation',
            'bourse',
            'international'
        ]);
        $table->text('description');
        $table->string('lieu', 150)->nullable();
        $table->string('pays', 80)->nullable();
        $table->decimal('budget', 10, 2)->unsigned()->nullable();
        $table->string('devise', 5)->default('XOF');
        $table->enum('categorie_cible', [
            'junior',
            'cadet',
            'senior',
            'veteran',
            'tous'
        ])->default('tous');
        $table->string('poste_cible', 60)->nullable();     // poste recherché si offre_club
        $table->string('sport_cible', 80)->nullable();     // sport concerné
        $table->unsignedSmallInteger('places_disponibles')->nullable();
        $table->date('date_limite')->nullable();            // date limite de candidature
        $table->tinyInteger('active')->default(1);
        $table->tinyInteger('mise_en_avant')->default(0);  // épinglée par l'admin
        $table->unsignedInteger('vues')->default(0);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['type', 'active']);
        $table->index(['categorie_cible', 'active']);
        $table->index('date_limite');
        $table->index('mise_en_avant');
        $table->index('pays');
    });
}

public function down(): void
{
    Schema::dropIfExists('opportunites');
}
};
