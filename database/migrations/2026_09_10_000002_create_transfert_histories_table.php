<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfert_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfert_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ancien_statut', 30)->nullable();
            $table->string('nouveau_statut', 30);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['transfert_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfert_histories');
    }
};
