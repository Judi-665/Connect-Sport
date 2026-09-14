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
    Schema::table('abonnements', function (Blueprint $table) {
        $table->foreignId('subscription_plan_id')
              ->nullable()
              ->after('club_id')
              ->constrained('subscription_plans')
              ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('abonnements', function (Blueprint $table) {
        $table->dropForeign(['subscription_plan_id']);
        $table->dropColumn('subscription_plan_id');
    });
}
};
