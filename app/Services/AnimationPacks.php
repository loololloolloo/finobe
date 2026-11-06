<?php

namespace App\Services;

/*
    AnimationPacks
     - which movement animations a player's character uses in game (picked on the Character page)
     - the game server asks /api/gameserver/animations/{userId} when a character spawns and swaps the
       AnimationIds on the character's Animate script; "default" leaves it as it is
     - the animation files are served from storage/rbx/files/animations (see rbxAPIs::asset)
*/
class AnimationPacks
{
    const DEFAULT = 'default';

    /** pack => [label, description, [Animate config value => [Animation child, asset id]]] */
    const PACKS = [
        'default' => [
            'label' => 'Default',
            'description' => 'The normal in-game movement animations.',
            'anims' => [],
        ],
        'test2014' => [
            'label' => '2014 Test Animations',
            'description' => "zeuxcg's leaked walk and climb tests from May 2014.",
            'anims' => [
                'walk' => ['WalkAnim', 157760076],
                'run' => ['RunAnim', 157760076],
                'climb' => ['ClimbAnim', 157760107],
            ],
        ],
    ];

    public static function exists(string $pack): bool
    {
        return isset(self::PACKS[$pack]);
    }

    public static function forUser($user): string
    {
        $avatar = is_array($user?->avatar ?? null) ? $user->avatar : [];
        $pack = (string) ($avatar[0]['animations'] ?? self::DEFAULT);

        return self::exists($pack) ? $pack : self::DEFAULT;
    }

    /** Lines of "<config> <Animation child> <url>" for the game server script (empty for default). */
    public static function gameServerLines(string $pack, string $baseUrl): string
    {
        $lines = [];
        foreach (self::PACKS[$pack]['anims'] ?? [] as $config => [$child, $assetId]) {
            $lines[] = "{$config} {$child} {$baseUrl}/asset/?id={$assetId}";
        }

        return implode("\n", $lines);
    }
}
