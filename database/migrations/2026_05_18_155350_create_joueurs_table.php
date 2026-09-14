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
    Schema::create('joueurs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('equipe_id')->nullable()->constrained()->nullOnDelete();
        $table->string('poste', 60)->nullable();          // gardien, ailier, pivot…
        $table->enum('categorie', ['junior', 'cadet', 'senior', 'veteran']);
        $table->date('date_naissance')->nullable();
        $table->string('nationalite', 60)->nullable();
        $table->string('telephone', 20)->nullable();
        $table->string('ville', 100)->nullable();
        $table->string('pays', 80)->default('Bénin');
        $table->text('bio')->nullable();
        $table->tinyInteger('sans_club')->default(0);         // visible recruteurs
        $table->tinyInteger('visible_recruteur')->default(0); // mis en avant
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['club_id', 'categorie']);
        $table->index('sans_club');
        $table->index('visible_recruteur');
        $table->index('poste');
    });
}

public function down(): void
{
    Schema::dropIfExists('joueurs');
}
};
