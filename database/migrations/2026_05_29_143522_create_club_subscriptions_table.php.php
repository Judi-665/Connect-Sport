<?php
// database/migrations/xxxx_create_club_subscriptions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('club_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('statut', ['actif', 'expire', 'suspendu'])->default('actif');
            $table->timestamp('debut_le');
            $table->timestamp('expire_le')->nullable();   // null = gratuit illimité
            $table->string('reference_paiement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_subscriptions');
    }
};