<?php

namespace App\Services;

use App\Http\Controllers\RobloxArbiterUtilities;
use App\Http\Controllers\RobloxUtilities;
use App\Http\Controllers\dataController as DataController;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * A saved outfit gets its own RCC thumbnail. Never change users.avatar or users.pfp
 * to render an outfit that the user is not currently wearing.
 */
class OutfitPreviewRenderer
{
    public static function render(User $user, int $outfitId): string
    {
        $outfit = DB::connection('finobe')->table('outfits')
            ->where('id', $outfitId)
            ->where('user_id', $user->id)
            ->first();

        if (!$outfit) {
            throw new \RuntimeException('Outfit not found.');
        }

        $relative = "outfits/{$outfitId}.png";
        $directory = config('finobe.cdn_path') . '/outfits';
        $destination = "{$directory}/{$outfitId}.png";

        // Outfits are immutable snapshots; their thumbnail can be reused forever.
        if (File::isFile($destination) && File::size($destination) > 8) {
            return $relative;
        }

        // CharacterFetch and BodyColors use this short-lived opaque token to read the
        // saved snapshot instead of the user's live avatar. Only the RCC sees the token.
        $token = bin2hex(random_bytes(16));
        $cacheKey = "outfit_thumbnail_token_{$token}";
        Cache::put($cacheKey, ['userId' => (int) $user->id, 'outfitId' => $outfitId], now()->addMinutes(5));

        try {
            $domain = env('FINOBE_DOMAIN', 'finobe.wtf');
            $site = "https://www.{$domain}";
            $arbiter = new RobloxArbiterUtilities(DataController::arbiter_pool(), (int) config('finobe.arbiter.port'));
            $job = $arbiter->ConstructJob(
                RobloxUtilities::GenerateGUID(),
                AvatarRenderer::SCRIPT,
                60,
                0,
                2,
                'ScriptExecution',
                ["{$site}/asset/CharacterFetch.ashx?userId={$user->id}&outfitToken={$token}", $site, 'PNG', 512, 512, AvatarRenderer::poseFor($user)]
            );

            $image = base64_decode((string) $arbiter->OpenJobEx($job), true);
        } finally {
            Cache::forget($cacheKey);
        }

        if (empty($image) || !str_starts_with($image, "\x89PNG\r\n\x1a\n")) {
            throw new \RuntimeException("Outfit {$outfitId} thumbnail render returned no PNG.");
        }

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0775, true);
        }

        if (File::put($destination, $image) === false) {
            throw new \RuntimeException("Could not write thumbnail for outfit {$outfitId}.");
        }

        return $relative;
    }

    public static function delete(int $outfitId): void
    {
        File::delete(config('finobe.cdn_path') . "/outfits/{$outfitId}.png");
    }
}
