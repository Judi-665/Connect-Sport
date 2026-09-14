<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('transferts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade');

        // Deux FK vers clubs — on les déclare manuellement car même table
        $table->unsignedBigInteger('club_source_id');
        $table->unsignedBigInteger('club_destinataire_id');

        $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();
        $table->enum('statut', [
            'en_attente',
            'en_negociation',
            'accepte',
            'refuse',
            'annule'
        ])->default('en_attente');
        $table->enum('type', ['definitif', 'pret', 'essai'])->default('definitif');

        // Montant du transfert (optionnel, peut rester confidentiel)
        $table->decimal('montant', 12, 2)->unsigned()->nullable();
        $table->string('devise', 5)->default('XOF');
        $table->tinyInteger('montant_confidentiel')->default(1);

        // Notes de chaque partie
        $table->text('note_joueur')->nullable();
        $table->text('note_club_source')->nullable();
        $table->text('note_club_destinataire')->nullable();

        // Dates importantes
        $table->date('date_effet')->nullable();            // date de prise d'effet
        $table->date('date_fin_pret')->nullable();         // si type = pret
        $table->timestamp('finalise_at')->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->foreign('club_source_id')
              ->references('id')->on('clubs')->onDelete('cascade');
        $table->foreign('club_destinataire_id')
              ->references('id')->on('clubs')->onDelete('cascade');

        $table->index(['joueur_id', 'statut']);
        $table->index(['club_source_id', 'statut']);
        $table->index(['club_destinataire_id', 'statut']);
        $table->index('type');
    });
}

public function down(): void
{
    Schema::dropIfExists('transferts');
}
};
