<?php
// database/migrations/xxxx_create_subscription_plans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                        // gratuit, standard, premium
            $table->string('slug')->unique();             // gratuit, standard, premium
            $table->integer('prix')->default(0);          // en FCFA
            $table->enum('frequence', ['mensuel', 'annuel', 'illimite'])->default('illimite');
            $table->integer('max_equipes')->default(1);   // 1 pour gratuit
            $table->boolean('multi_equipes')->default(false);
            $table->boolean('gestion_licences')->default(false);
            $table->boolean('agenda')->default(false);
            $table->boolean('stats_avancees')->default(false);
            $table->boolean('stockage_etendu')->default(false);
            $table->boolean('mise_en_avant')->default(false);
            $table->boolean('outils_marketing')->default(false);
            $table->boolean('galerie_media')->default(false);
            $table->boolean('notifications_ciblees')->default(false);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};