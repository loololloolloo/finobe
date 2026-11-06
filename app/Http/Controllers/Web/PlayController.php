<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/*
    PlayController (play.<domain>)
     - /games?id=<placeId>: the 2016 web client with a the 2016 client's loading screen (game name, creator)
     - /webplayer/<file>: the web client files from storage/rbx/webplayer
     - the play host doesn't share the site's login cookie, so the site's /app/place/<id>/browser hands over
       a one-time code (?code=) that logs the player in here
     - the client needs SharedArrayBuffer, so both are sent cross-origin isolated (COOP + COEP)
*/
class PlayController extends Controller
{
    const FILES = [
        'WebPlayer.js' => 'text/javascript',
        'WebPlayer.worker.js' => 'text/javascript',
        'WebPlayer.wasm' => 'application/wasm',
        'WebPlayer.data' => 'application/octet-stream',
    ];

    const ISOLATION = [
        'Cross-Origin-Opener-Policy' => 'same-origin',
        'Cross-Origin-Embedder-Policy' => 'credentialless',
    ];

    /** finobe.wtf/app/place/<id>/browser: send a logged-in player to the play host with a one-time code */
    public function handoff(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        $code = Str::random(48);
        Cache::put("play_handoff_{$code}", Auth::id(), 120);

        $domain = env('FINOBE_DOMAIN', 'finobe.wtf');
        return redirect("https://play.{$domain}/games?id=" . (int) $id . "&code={$code}");
    }

    public function games(Request $request)
    {
        $domain = env('FINOBE_DOMAIN', 'finobe.wtf');
        $id = (int) $request->query('id', 0);

        // the web client only runs on https (SharedArrayBuffer needs a secure context)
        if (!$request->secure() && $request->header('X-Forwarded-Proto') !== 'https' && !str_contains((string) $request->header('CF-Visitor'), 'https')) {
            return redirect("https://play.{$domain}/games?id={$id}");
        }

        $code = (string) $request->query('code', '');
        if ($code !== '') {
            if ($userId = Cache::pull("play_handoff_{$code}")) {
                Auth::loginUsingId($userId);
                $request->session()->regenerate();
            }
            return redirect("/games?id={$id}&h=1"); // drop the code from the address bar
        }

        $user = Auth::user();
        if (!$user) {
            // h=1: we just tried a code and still aren't logged in (expired code or the session cookie didn't stick)
            if ($request->query('h')) {
                return response("Couldn't log you in on play.{$domain}. Go back to the game page and press Play in browser again.", 403);
            }
            return redirect("https://{$domain}/app/place/{$id}/browser");
        }

        $place = Asset::where('id', $id)->where('asset_type', 9)->first();
        if (!$place) {
            abort(404);
        }

        return response()->view('v2/Play/Games', [
            'place' => [
                'id' => $place->id,
                'title' => strip_tags((string) $place->title),
                'creator' => User::where('id', $place->author)->value('username') ?? 'Unknown',
                'creatorId' => $place->author,
            ],
            'joinScript' => "https://assetgame.{$domain}/game/placelauncher.ashx?request=RequestGame&placeId={$place->id}&ticket=" . urlencode((string) $user->token),
            'domain' => $domain,
            'back' => "https://{$domain}/app/place/{$place->id}",
        ])->withHeaders(self::ISOLATION);
    }

    public function webplayer(string $file)
    {
        if (!isset(self::FILES[$file])) {
            abort(404);
        }
        $path = storage_path("rbx/webplayer/{$file}");
        if (!is_file($path)) {
            abort(404);
        }

        return response()->file($path, self::ISOLATION + [
            'Content-Type' => self::FILES[$file],
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
