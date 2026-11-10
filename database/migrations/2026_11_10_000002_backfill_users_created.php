<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Give the join-date column a default so raw inserts always get one.
        Schema::connection('finobe')->table('users', function (Blueprint $table) {
            $table->timestamp('created')->useCurrent()->nullable()->change();
        });

        // Backfill rows created before the column had a default. The invite key a user
        // redeemed is the most precise join evidence; fall back to created_at, then now.
        DB::connection('finobe')->statement(
            'UPDATE users u '
            . 'LEFT JOIN invitekeys k ON k.usedBy = u.username AND k.used = \'y\' '
            . 'SET u.created = COALESCE(k.dateUsed, u.created_at, CURRENT_TIMESTAMP) '
            . 'WHERE u.created IS NULL OR u.created <= \'1970-01-02 00:00:00\''
        );
    }

    public function down(): void
    {
        // no-op: the column and its default are harmless to keep
    }
};
