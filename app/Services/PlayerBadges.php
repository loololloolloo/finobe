<?php

namespace App\Services;

/*
    PlayerBadges
     - which icon a player gets next to their name in the 2016 in-game player list.
       The stock 2016 client picks the icon from the player's MembershipType (set by the join script)
       and from the "is in group 1200769" admin check, so special badges ride on those:
         user 1  -> the duck       (OBC slot: content/textures/ui/icon_OBC-16.png)
         user 2  -> custom badge   (TBC slot: content/textures/ui/icon_TBC-16.png)
         admins  -> normal admin badge (icon_admin-16.png)
       The images live in the client (see tools/client/patch_client.py).
*/
class PlayerBadges
{
    const SPECIAL = [
        1 => 'OutrageousBuildersClub',
        2 => 'TurboBuildersClub',
    ];

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
