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
    Schema::create('parents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');   // compte parent
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade'); // joueur mineur suivi
        $table->enum('lien', ['pere', 'mere', 'tuteur', 'autre'])->default('pere');
        $table->tinyInteger('acces_stats')->default(1);   // peut voir les stats du joueur
        $table->tinyInteger('acces_agenda')->default(1);  // peut voir l'agenda du club
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();

        $table->unique(['user_id', 'joueur_id']);         // un parent ne se lie qu'une fois au même joueur
        $table->index('joueur_id');
    });
}

public function down(): void
{
    Schema::dropIfExists('parents');
}
};
