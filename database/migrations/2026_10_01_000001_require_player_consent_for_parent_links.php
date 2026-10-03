<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing self-created links have no stored proof of consent. Revoke access
        // until the player explicitly approves each request in the application.
        DB::table('parents')->where('actif', true)->update([
            'actif' => false,
            'acces_stats' => false,
            'acces_agenda' => false,
        ]);
    }

    public function down(): void
    {
        // Deliberately do not re-enable links without consent when rolling back.
    }
};
