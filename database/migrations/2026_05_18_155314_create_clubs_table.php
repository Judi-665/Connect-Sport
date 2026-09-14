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
    Schema::create('clubs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');   // compte admin du club
        $table->foreignId('sport_id')->constrained()->onDelete('restrict'); // sport pratiqué
        $table->string('nom', 120);
        $table->string('slug', 120)->unique();
        $table->string('ville', 100)->nullable();
        $table->string('pays', 80)->default('Bénin');
        $table->string('adresse', 255)->nullable();
        $table->text('description')->nullable();
        $table->string('logo', 255)->nullable();
        $table->string('site_web', 255)->nullable();
        $table->string('telephone', 20)->nullable();
        $table->enum('abonnement', ['gratuit', 'standard', 'premium'])->default('gratuit');
        $table->timestamp('abonnement_expire_at')->nullable();
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['ville', 'sport_id']);
        $table->index('abonnement');
        $table->index('actif');
    });
}

public function down(): void
{
    Schema::dropIfExists('clubs');
}
};
