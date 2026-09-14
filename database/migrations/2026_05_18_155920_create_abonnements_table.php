<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('abonnements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->constrained()->onDelete('cascade');

        // Plan souscrit
        $table->enum('plan', ['gratuit', 'standard', 'premium']);
        $table->decimal('montant', 8, 2)->unsigned()->nullable();
        $table->string('devise', 5)->default('XOF');

        // Données FedaPay
        $table->string('fedapay_transaction_id', 100)->nullable()->unique();
        $table->string('fedapay_token', 255)->nullable();
        $table->string('fedapay_reference', 100)->nullable();

        // Mode de paiement
        $table->enum('mode_paiement', [
            'mobile_money',
            'carte_bancaire',
            'virement',
            'autre'
        ])->nullable();

        $table->string('numero_telephone_paiement', 20)->nullable();

        // Statut et durée
        $table->enum('statut', [
            'en_attente',
            'actif',
            'expire',
            'annule',
            'rembourse'
        ])->default('en_attente');

        $table->timestamp('debut_at')->nullable();
        $table->timestamp('fin_at')->nullable();
        $table->tinyInteger('renouvellement_auto')->default(0);
        $table->timestamp('renouvele_at')->nullable();     // date du dernier renouvellement

        // Rappels envoyés
        $table->tinyInteger('rappel_7j_envoye')->default(0);  // rappel 7j avant expiration
        $table->tinyInteger('rappel_1j_envoye')->default(0);  // rappel 1j avant expiration

        $table->text('metadata')->nullable();              // réponse brute FedaPay
        $table->text('note')->nullable();
        $table->timestamps();

        $table->index(['club_id', 'statut']);
        $table->index(['club_id', 'plan']);
        $table->index('fin_at');                           // pour le cron ExpireAbonnements
        $table->index('statut');
        $table->index('renouvellement_auto');
    });
}

public function down(): void
{
    Schema::dropIfExists('abonnements');
}
};
