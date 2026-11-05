<?php

namespace App\Services;

use App\Models\User;
use App\Http\Controllers\RobloxUtilities;
use App\Http\Controllers\RobloxArbiterUtilities;
use App\Http\Controllers\dataController as DataController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

/*
    AvatarRenderer
     - asks the arbiter (RCC) to render a user's character and makes it their avatar (users.pfp)
*/
class AvatarRenderer
{
    const DEFAULT_PFP = 'avatar/682b8bd6cbc48.png';

    const SCRIPT = '
        local characterUrl, url, fileExtension, x, y = ...

        settings()["Task Scheduler"].ThreadPoolConfig = Enum.ThreadPoolConfig.PerCore4;
        game:GetService("ContentProvider"):SetThreadPool(16)
        game:GetService("ScriptContext").ScriptsDisabled = true
        pcall(function() game:GetService("ContentProvider"):SetBaseUrl(url) end)

        local player = game:GetService("Players"):CreateLocalPlayer(0)
        player.CharacterAppearance = characterUrl
        player:LoadCharacter(false)

        if player.Character then
            for _, child in pairs(player.Character:GetChildren()) do
                if child:IsA("Tool") then
                    player.Character.Torso["Right Shoulder"].CurrentAngle = math.rad(90)
                    break
                end
            end
        end

        game:GetService("ThumbnailGenerator").GraphicsMode = 4
        return game:GetService("ThumbnailGenerator"):Click(fileExtension, x, y, true)
    ';

    /**
     * True when the user has no real render yet: still on the default picture,
     * no picture at all, or the file is missing from the CDN.
     */
    public static function needsRender(?string $pfp): bool
    {
        if (!$pfp || $pfp === self::DEFAULT_PFP) {
            return true;
        }

        return !File::exists(config('finobe.cdn_path') . '/avatar/' . basename($pfp));
    }

    /**
     * Queues a background render for a user whose avatar isn't rendered.
     * At most one attempt per user every 10 minutes, so a down arbiter isn't hammered.
     */
    public static function renderIfMissing(int $userId, ?string $pfp): void
    {
        if (self::needsRender($pfp) && Cache::add("avatar_autorender_{$userId}", true, 600)) {
            \App\Jobs\RenderAvatar::dispatchAfterResponse($userId);
        }
    }

    /**
     * Renders the user's current outfit, stores it on the CDN and points users.pfp at it.
     * Returns the new pfp path (e.g. "avatar/abc123.png").
     */
    public static function render(User $user): string
    {
        $domain = env('FINOBE_DOMAIN', 'finobe.wtf');
        $site = "https://www.{$domain}";

        $arbiter = new RobloxArbiterUtilities(DataController::arbiter_pool(), (int) config('finobe.arbiter.port'));
        $job = $arbiter->ConstructJob(
            RobloxUtilities::GenerateGUID(),
            self::SCRIPT,
            60,
            0,
            2,
            "ScriptExecution",
            ["{$site}/asset/CharacterFetch.ashx?userId={$user->id}", $site, "PNG", 768, 768]
        );

        $image = base64_decode((string) $arbiter->OpenJobEx($job), true);

        if (empty($image) || !str_starts_with($image, "\x89PNG")) {
            throw new \RuntimeException("Avatar render for user {$user->id} returned no image (is the arbiter running?)");
        }

        $filename = uniqid() . '.png';
        $directory = config('finobe.cdn_path') . '/avatar';

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0775, true);
        }

        File::put("{$directory}/{$filename}", $image);

        // delete the previous render, never the shared default
        $old = $user->fresh()->pfp;
        if ($old && $old !== self::DEFAULT_PFP && str_starts_with($old, 'avatar/')) {
            File::delete(config('finobe.cdn_path') . '/avatar/' . basename($old));
        }

        User::where('id', $user->id)->update(['pfp' => "avatar/{$filename}"]);

        return "avatar/{$filename}";
    }
}
