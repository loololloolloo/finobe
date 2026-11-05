<?php

namespace App\Services;

/*
    PlayerBadges
     - what decides the icon next to a player's name in the 2016 in-game player list:
         user 1          -> Finobe's duck       } built into the client's player list
         user 2          -> their custom badge   } (CUSTOM_ICONS, see tools/client/patch_client.py)
         other admins    -> normal admin badge (the site answers "in group 1200769" for status 'admin')
         BC / TBC / OBC  -> membership badges, from MembershipType in the join script
*/
class PlayerBadges
{
    // no per-user special badges with the stock client (see above)
    const SPECIAL = [];

    /** MembershipType for the join script. */
    public static function membershipType(int $userId): string
    {
        return self::SPECIAL[$userId] ?? 'None';
    }

    /** Whether the admin icon should show. Users with a special badge don't get it (it would replace theirs). */
    public static function showsAdminIcon(int $userId, ?string $status): bool
    {
        return $status === 'admin' && !isset(self::SPECIAL[$userId]);
    }
}
