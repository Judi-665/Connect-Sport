<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_club', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('statut', 30)->default('partenaire');
            $table->timestamp('dernier_contact_at')->nullable();
            $table->timestamps();
            $table->unique(['agent_id', 'club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_club');
    }
};
