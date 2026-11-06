<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
    PlayController (play.<domain>)
     - /games?id=<placeId>: the 2016 web client with a 2016-style loading screen (game render, name, creator)
     - /webplayer/<file>: the web client files from storage/rbx/webplayer
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

    public function games(Request $request)
    {
        $domain = env('FINOBE_DOMAIN', 'finobe.wtf');
        $user = Auth::user();
        if (!$user) {
            return redirect("https://{$domain}/login");
        }

        $id = (int) $request->query('id', 0);
        $place = Asset::where('id', $id)->where('asset_type', 9)->first();
        if (!$place) {
            abort(404);
        }
        $additional = is_array($place->additional) ? $place->additional : (json_decode((string) $place->additional, true) ?: []);

        $thumb = \App\Services\PlaceThumbnail::url($additional);
        if (str_starts_with($thumb, '/')) {
            $thumb = "https://www.{$domain}{$thumb}";
        }

        return response()->view('v2/Play/Games', [
            'place' => [
                'id' => $place->id,
                'title' => strip_tags((string) $place->title),
                'creator' => User::where('id', $place->author)->value('username') ?? 'Unknown',
                'creatorId' => $place->author,
                'thumbnail' => $thumb,
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
