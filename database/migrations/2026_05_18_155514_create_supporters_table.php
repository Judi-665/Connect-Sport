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
    // Profil supporter
    Schema::create('supporters', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('ville', 100)->nullable();
        $table->string('pays', 80)->default('Bénin');
        $table->text('bio')->nullable();
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();

        $table->index('user_id');
    });

    // Table pivot supporter <-> club (abonnement supporter)
    Schema::create('club_supporter', function (Blueprint $table) {
        $table->foreignId('club_id')->constrained()->onDelete('cascade');
        $table->foreignId('supporter_id')->constrained()->onDelete('cascade');
        $table->enum('type_abonnement', ['gratuit', 'premium'])->default('gratuit');
        $table->timestamp('abonnement_expire_at')->nullable();
        $table->tinyInteger('notifications_actives')->default(1); // recevoir alertes du club
        $table->timestamps();

        $table->primary(['club_id', 'supporter_id']);
        $table->index('type_abonnement');
    });
}

public function down(): void
{
    Schema::dropIfExists('club_supporter');
    Schema::dropIfExists('supporters');  // ⚠️ drop pivot d'abord (FK)
}
};
