<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // database/migrations/2024_04_01_000002_create_paiements_formations_table.php

public function up(): void
{
    Schema::create('paiements_formations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('joueur_id')->constrained()->onDelete('cascade');
        $table->foreignId('formation_id')->constrained()->onDelete('cascade');

        // Données FedaPay
        $table->string('fedapay_transaction_id', 100)->nullable()->unique();
        $table->string('fedapay_token', 255)->nullable();
        $table->string('fedapay_reference', 100)->nullable(); // référence interne FedaPay

        // Montant
        $table->decimal('montant', 10, 2)->unsigned();
        $table->string('devise', 5)->default('XOF');

        // Statut
        $table->enum('statut', [
            'en_attente',
            'reussi',
            'echoue',
            'rembourse',
            'annule'
        ])->default('en_attente');

        // Mode de paiement (renseigné après callback FedaPay)
        $table->enum('mode_paiement', [
            'mobile_money',
            'carte_bancaire',
            'virement',
            'autre'
        ])->nullable();

        $table->string('numero_telephone_paiement', 20)->nullable(); // si mobile money
        $table->text('metadata')->nullable();              // réponse brute FedaPay (JSON)
        $table->timestamp('paye_at')->nullable();
        $table->timestamp('rembourse_at')->nullable();
        $table->text('note')->nullable();                  // raison echec ou remboursement
        $table->timestamps();

        $table->index(['joueur_id', 'statut']);
        $table->index(['formation_id', 'statut']);
        $table->index('fedapay_transaction_id');
        $table->index('mode_paiement');
        $table->index('paye_at');
    });
}

public function down(): void
{
    Schema::dropIfExists('paiements_formations');
}
};
