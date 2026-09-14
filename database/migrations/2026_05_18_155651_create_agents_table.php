<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('agents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('numero_accreditation', 80)->nullable()->unique();
        $table->string('agence', 120)->nullable();
        $table->string('ville', 100)->nullable();
        $table->string('pays', 80)->default('Bénin');
        $table->string('telephone', 20)->nullable();
        $table->text('bio')->nullable();
        $table->string('site_web', 255)->nullable();
        $table->tinyInteger('verifie')->default(0);        // agent vérifié par l'admin
        $table->tinyInteger('actif')->default(1);
        $table->timestamps();
        $table->softDeletes();

        $table->index('verifie');
        $table->index('actif');
    });
}

public function down(): void
{
    Schema::dropIfExists('agents');
}
};
