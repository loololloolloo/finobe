<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/*
    Host2012
     - 2012 game servers: there's no 2012 RCC, so tools/gameserver2012/host2012.ps1 (on the game server
       machine) starts the 2012 client with -script <GameServer2012.ashx URL> and ends it again
     - the script URL carries a one-time token instead of the access key (Client\GameServer2012Controller)
*/
class Host2012
{
    public static function configured(): bool
    {
        return config('finobe.host2012.url') !== '';
    }

    /** Asks host2012 to start a server for this job. Throws on failure. */
    public static function start(string $jobId): void
    {
        $token = bin2hex(random_bytes(24));
        Cache::put("gameserver2012_{$token}", $jobId, 600);

        $domain = env('FINOBE_DOMAIN', 'finobe.wtf');
        // http: the 2012 client's old libcurl/OpenSSL can't be relied on for modern TLS
        $scriptUrl = "http://assetgame.{$domain}/game/gameserver2012.ashx?token={$token}";

        $r = self::client()->post(config('finobe.host2012.url') . '/start', ['jobId' => $jobId, 'scriptUrl' => $scriptUrl]);
        if (!$r->successful()) {
            throw new \RuntimeException("host2012 /start answered {$r->status()}: " . substr($r->body(), 0, 200));
        }
    }

    /** Ends the server process for this job (no error if it's already gone). */
    public static function stop(string $jobId): void
    {
        if (self::configured()) {
            self::client()->post(config('finobe.host2012.url') . '/stop', ['jobId' => $jobId]);
        }
    }

    private static function client()
    {
        return Http::timeout(15)->withHeaders(['AccessKey' => (string) env('FINOBE_CLOUD_AUTHORIZATION', '')])->asJson();
    }
}
