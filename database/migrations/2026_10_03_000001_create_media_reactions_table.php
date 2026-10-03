<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('medias')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['like', 'love']);
            $table->timestamps();

            $table->unique(['media_id', 'user_id']);
            $table->index(['media_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_reactions');
    }
};