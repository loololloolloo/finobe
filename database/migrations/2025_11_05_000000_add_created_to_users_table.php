<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The app reads users.created (join date) but the original migration only
     * created created_at, and User has timestamps disabled, so nothing set it.
     */
    public function up(): void
    {
        if (!Schema::connection('finobe')->hasColumn('users', 'created')) {
            Schema::connection('finobe')->table('users', function (Blueprint $table) {
                $table->timestamp('created')->useCurrent();
            });
        }

        // Fix rows that were created without a join date.
        DB::connection('finobe')->table('users')
            ->whereNull('created')
            ->orWhere('created', '<=', '1970-01-02 00:00:00')
            ->update(['created' => DB::raw('COALESCE(created_at, NOW())')]);
    }

    public function down(): void
    {
        // intentionally left empty: the column may have existed before this migration
    }
};
