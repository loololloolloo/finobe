<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AvatarRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

/*
    RenderAvatar
     - re-renders a user's avatar after they sign up or change their outfit.
     - dispatched with dispatchAfterResponse(), so it runs right after the page is sent
       (no queue worker needed) and the user never waits on RCC.
     - while one render runs for a user, further requests only mark it "dirty"; the running
       render then does one more pass so the final image matches the latest outfit.
*/
class RenderAvatar
{
    use Dispatchable, Queueable;

    public function __construct(public int $userId)
    {
    }

    public function handle(): void
    {
        $dirtyKey = "avatar_render_dirty_{$this->userId}";
        Cache::put($dirtyKey, true, 300);

        $lock = Cache::lock("avatar_render_{$this->userId}", 120);

        if (!$lock->get()) {
            return; // a render is already running; it will pick up the dirty flag
        }

        try {
            $passes = 0;

            while (Cache::pull($dirtyKey) && $passes++ < 3) {
                $user = User::find($this->userId);

                if (!$user) {
                    return;
                }

                AvatarRenderer::render($user);
            }
        } catch (\Throwable $e) {
            report($e);
        } finally {
            $lock->release();
        }
    }
}
