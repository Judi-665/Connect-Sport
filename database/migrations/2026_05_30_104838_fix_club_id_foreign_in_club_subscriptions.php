<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('club_subscriptions', function (Blueprint $table) {
            // Supprimer l'ancienne clé étrangère
            $table->dropForeign(['club_id']);

            // Recréer vers clubs
            $table->foreign('club_id')
                  ->references('id')
                  ->on('clubs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('club_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->foreign('club_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};