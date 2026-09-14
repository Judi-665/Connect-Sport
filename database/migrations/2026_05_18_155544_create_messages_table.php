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
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('expediteur_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete(); // contexte club si message lié à un club
        $table->text('contenu');
        $table->tinyInteger('lu')->default(0);
        $table->timestamp('lu_at')->nullable();            // date de lecture exacte
        $table->tinyInteger('archive_expediteur')->default(0);
        $table->tinyInteger('archive_destinataire')->default(0);
        $table->timestamps();
        $table->softDeletes();

        $table->index(['expediteur_id', 'destinataire_id']);
        $table->index(['destinataire_id', 'lu']);          // pour le badge "non lus"
        $table->index('club_id');
    });
}

public function down(): void
{
    Schema::dropIfExists('messages');
}
};
