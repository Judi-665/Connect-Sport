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
    Schema::create('sports', function (Blueprint $table) {
        $table->id();
        $table->string('nom', 80)->unique();
        $table->string('slug', 80)->unique();
        $table->string('icone', 50)->nullable();   // ex: "⚽" ou nom d'icône Bootstrap
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('sports');
}
};
