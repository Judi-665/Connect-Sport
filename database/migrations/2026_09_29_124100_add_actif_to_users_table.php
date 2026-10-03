<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'actif')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('actif')->default(true)->after('telephone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'actif')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('actif');
            });
        }
    }
};
