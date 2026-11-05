<?php

namespace App\Services;

/*
    PlayerBadges
     - what decides the icon next to a player's name in the 2016 in-game player list.
       The stock 2016 client picks it from the player's MembershipType (sent in the join script:
       BC / TBC / OBC are normal membership badges) and from the "is in group 1200769" admin check
       (admins get the admin badge). Per-user badges (e.g. a duck for one user) aren't possible with
       the stock client: its player-list script is compiled and encrypted into the exe.
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
