<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
    Restrictions
     - admins can restrict a user from chatting (sending messages), the forum (posting and replying)
       or playing (joining games), for a while or until lifted, on the user's moderation page
     - Settings > Security > Restrictions shows the user their own
*/
class Restrictions
{
    const TYPES = ['chat' => 'Chat', 'forum' => 'Forum', 'play' => 'Playing'];

    private static function table()
    {
        return DB::connection('finobe')->table('user_restrictions');
    }

    public static function available(): bool
    {
        static $has = null;
        return $has ??= Schema::connection('finobe')->hasTable('user_restrictions');
    }

    /** the restriction in force for this user and type, or null */
    public static function active(int $userId, string $type): ?object
    {
        if (!self::available()) {
            return null;
        }
        return self::table()->where('user_id', $userId)->where('type', $type)->whereNull('lifted_at')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByRaw('expires_at IS NULL DESC')->orderByDesc('expires_at')->first();
    }

    /** "You're restricted from the forum until ...: reason" for showing to the user, or null */
    public static function message(int $userId, string $type): ?string
    {
        $r = self::active($userId, $type);
        if (!$r) {
            return null;
        }
        $what = ['chat' => 'sending messages', 'forum' => 'posting on the forum', 'play' => 'playing games'][$type];
        $until = $r->expires_at ? ' until ' . \Carbon\Carbon::parse($r->expires_at)->format('m/d/Y h:i A') : '';
        return "You're restricted from {$what}{$until}. Reason: {$r->reason}";
    }

    public static function add(int $userId, string $type, string $reason, ?int $days, ?int $moderatorId): void
    {
        self::table()->insert([
            'user_id' => $userId, 'type' => $type, 'reason' => $reason, 'moderator_id' => $moderatorId,
            'expires_at' => $days ? now()->addDays($days) : null, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public static function lift(int $userId, string $type): int
    {
        return self::table()->where('user_id', $userId)->where('type', $type)->whereNull('lifted_at')
            ->update(['lifted_at' => now(), 'updated_at' => now()]);
    }
}
