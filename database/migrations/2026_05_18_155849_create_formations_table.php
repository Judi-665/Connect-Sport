<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('formations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete(); // null si créée par admin
        $table->foreignId('user_id')->constrained()->onDelete('cascade');        // créateur
        $table->string('titre', 150);
        $table->text('description');
        $table->enum('type', ['technique', 'physique', 'tactique', 'mental']);
        $table->enum('niveau', ['debutant', 'intermediaire', 'avance'])->default('intermediaire');
        $table->enum('categorie_cible', [
            'junior',
            'cadet',
            'senior',
            'veteran',
            'tous'
        ])->default('tous');
        $table->string('sport_cible', 80)->nullable();
        $table->string('duree_estimee', 50)->nullable();   // ex : "45 min", "2h"
        $table->string('video_url', 500)->nullable();
        $table->string('miniature', 500)->nullable();
        $table->decimal('prix', 8, 2)->unsigned()->default(0.00);
        $table->string('devise', 5)->default('XOF');
        $table->tinyInteger('gratuit')->default(1);
        $table->enum('visibilite', ['public', 'club', 'premium'])->default('public');
        $table->tinyInteger('active')->default(1);
        $table->tinyInteger('mise_en_avant')->default(0);
        $table->unsignedInteger('vues')->default(0);
        $table->unsignedInteger('telechargements')->default(0);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['type', 'active']);
        $table->index(['gratuit', 'active']);
        $table->index('categorie_cible');
        $table->index('niveau');
        $table->index('mise_en_avant');
    });
}

public function down(): void
{
    Schema::dropIfExists('formations');
}
};
