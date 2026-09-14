<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->enum('canal', ['app', 'email', 'sms'])->default('app');
            $table->tinyInteger('vue')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'vue']);
            $table->index('canal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};