<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('sponsors', function (Blueprint $table) {
        $table->id();
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->string('nom', 120);
        $table->string('logo', 255)->nullable();
        $table->string('site_web', 255)->nullable();
        $table->string('email_contact', 150)->nullable();
        $table->string('telephone_contact', 20)->nullable();
        $table->text('description')->nullable();
        $table->enum('type_visibilite', [
            'maillot_domicile',
            'maillot_exterieur',
            'pancarte',
            'digital',
            'tous'
        ])->default('digital');
        $table->decimal('montant_contrat', 12, 2)->unsigned()->nullable();
        $table->string('devise', 5)->default('XOF');
        $table->tinyInteger('montant_confidentiel')->default(1);
        $table->date('debut_partenariat')->nullable();
        $table->date('fin_partenariat')->nullable();
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['club_id', 'actif']);
        $table->index('type_visibilite');
        $table->index('fin_partenariat');                  // pour alerter avant expiration
    });
}

public function down(): void
{
    Schema::dropIfExists('sponsors');
}
};
