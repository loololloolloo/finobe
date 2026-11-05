<?php

namespace App\Services\Matchmaking;

use App\Jobs\StartGameServer;
use App\Services\Matchmaking\Enums\PlaceLauncherStatusCodes as Status;
use App\Services\PlaceAccess;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/*
    PlaceLauncher
     - what the client polls (Game/PlaceLauncher.ashx) until it gets a server to join
     - servers.status: 1 = RCC is starting it, 2 = running (the game server called /api/gameserver/register)
     - a server is started in the background (StartGameServer), the client keeps polling while it loads
*/
class PlaceLauncher
{
    // how long a server may take to call /register before it's treated as dead
    const START_TIMEOUT = 120;
    // a running server that hasn't called /alive for this long is treated as dead
    const ALIVE_TIMEOUT = 150;
    // after a failed start, don't retry for this long (the client gets an error instead of loading forever)
    const FAIL_COOLDOWN = 45;

    /**
     * @return array{status: Status, jobId: string, message: string}
     */
    public static function request(int $placeId, object $user, ?string $jobId = null): array
    {
        $db = DB::connection('finobe');
        $place = $db->table('assets')->where('id', $placeId)->where('asset_type', 9)->first();

        if (!$place) {
            return self::result(Status::Error, '', "This game doesn't exist.");
        }

        $additional = json_decode($place->additional ?? '{}', true) ?: [];

        if (($additional['allowplaying'] ?? true) === false && (int) $place->author !== (int) $user->id) {
            return self::result(Status::Restricted, '', "This game isn't open right now.");
        }

        if ($reason = PlaceAccess::denyReason($place, $user)) {
            return self::result(Status::Restricted, '', $reason);
        }

        if (!self::gameServerHost()) {
            return self::result(Status::Error, '', 'Game servers are not set up yet, try again later.');
        }

        self::cleanup();

        $maxPlayers = max(1, (int) ($additional['maxplayers'] ?? 15));
        $servers = $db->table('servers')->where('placeid', $placeId)->whereIn('status', ['1', '2'])->get();

        // joining a specific server (from a profile's "join" button)
        if ($jobId !== null && $jobId !== '') {
            $server = $servers->firstWhere('jobId', $jobId);

            if (!$server) {
                return self::result(Status::GameEnded, $jobId, 'That server has shut down.');
            }
            if ((string) $server->status !== '2') {
                return self::result(Status::Loading, $jobId, 'The server is starting...');
            }
            if (self::playerCount($server) >= $maxPlayers) {
                return self::result(Status::GameFull, $jobId, 'That server is full.');
            }

            return self::ready($server, $user);
        }

        // the fullest running server that still has room
        $running = $servers->filter(fn($s) => (string) $s->status === '2' && self::playerCount($s) < $maxPlayers)
            ->sortByDesc(fn($s) => self::playerCount($s));

        if ($server = $running->first()) {
            return self::ready($server, $user);
        }

        // one is already starting: keep the client waiting on it
        if ($server = $servers->firstWhere('status', '1')) {
            return self::result(Status::Loading, $server->jobId, 'The server is starting...');
        }

        if ($error = Cache::get("gameserver_failed_{$placeId}")) {
            return self::result(Status::Error, '', $error);
        }

        // nothing running: start one
        $lock = Cache::lock("gameserver_start_{$placeId}", 10);
        if (!$lock->get()) {
            return self::result(Status::Waiting, '', 'Looking for a server...');
        }

        try {
            $newJobId = self::generateJobId();
            $db->table('servers')->insert([
                'ip' => self::gameServerHost(),
                'port' => self::freePort(),
                'placeid' => $placeId,
                'players' => '[]',
                'jobId' => $newJobId,
                'status' => '1',
                'started_at' => now(),
                'last_alive' => null,
            ]);
        } catch (\RuntimeException $e) {
            return self::result(Status::GameFull, '', 'All game servers are busy, try again in a bit.');
        } finally {
            $lock->release();
        }

        StartGameServer::dispatchAfterResponse($newJobId);

        return self::result(Status::Waiting, $newJobId, 'Starting a server...');
    }

    /** Marks a start as failed: removes the server and makes the place's next requests show an error for a bit. */
    public static function startFailed(string $jobId, string $message = "Couldn't start a server, try again in a bit."): void
    {
        $server = DB::connection('finobe')->table('servers')->where('jobId', $jobId)->first();
        if (!$server) {
            return;
        }

        DB::connection('finobe')->table('servers')->where('jobId', $jobId)->delete();
        Cache::put("gameserver_failed_{$server->placeid}", $message, self::FAIL_COOLDOWN);
    }

    /** The address players connect to (the RCC machine's public IP). Null if it isn't configured. */
    public static function gameServerHost(): ?string
    {
        $host = trim((string) config('finobe.gameserver.host'));

        if ($host === '' || in_array($host, ['127.0.0.1', 'localhost', '::1', '0.0.0.0'], true)) {
            // only fine when the client runs on the same machine (local testing)
            return config('app.debug') && $host !== '' ? $host : null;
        }

        return $host;
    }

    /** Removes servers that never finished starting or stopped calling /alive. */
    public static function cleanup(): void
    {
        $db = DB::connection('finobe');

        $db->table('servers')->where('status', '1')
            ->where(fn($q) => $q->whereNull('started_at')->orWhere('started_at', '<', now()->subSeconds(self::START_TIMEOUT)))
            ->delete();

        $db->table('servers')->where('status', '2')
            ->where(fn($q) => $q->whereNull('last_alive')->orWhere('last_alive', '<', now()->subSeconds(self::ALIVE_TIMEOUT)))
            ->where(fn($q) => $q->whereNull('started_at')->orWhere('started_at', '<', now()->subSeconds(self::ALIVE_TIMEOUT)))
            ->delete();
    }

    private static function ready(object $server, object $user): array
    {
        // one-time ticket so Join.ashx works even if the client's session cookie didn't stick
        $ticket = bin2hex(random_bytes(24));
        Cache::put("join_ticket_{$ticket}", ['user' => (int) $user->id, 'jobId' => $server->jobId], 300);

        $domain = env('FINOBE_DOMAIN', 'finobe.wtf');

        return self::result(Status::Joining, $server->jobId, '', [
            'joinScriptUrl' => "https://assetgame.{$domain}/Game/Join.ashx?jobId=" . urlencode($server->jobId) . "&ticket={$ticket}",
            'authenticationUrl' => "https://www.{$domain}/Login/Negotiate.ashx",
            'authenticationTicket' => (string) ($user->token ?? ''),
        ]);
    }

    private static function result(Status $status, string $jobId, string $message, array $extra = []): array
    {
        return ['status' => $status, 'jobId' => $jobId, 'message' => $message] + $extra + [
            'joinScriptUrl' => '',
            'authenticationUrl' => '',
            'authenticationTicket' => '',
        ];
    }

    private static function playerCount(object $server): int
    {
        $players = json_decode((string) $server->players, true);
        return is_array($players) ? count($players) : 0;
    }

    private static function freePort(): int
    {
        $min = (int) config('finobe.gameserver.port_min');
        $max = (int) config('finobe.gameserver.port_max');

        $used = DB::connection('finobe')->table('servers')->pluck('port')->map(fn($p) => (int) $p)->all();
        $free = array_values(array_diff(range($min, $max), $used));

        if (empty($free)) {
            throw new \RuntimeException('No free game server ports in the configured range');
        }

        return $free[random_int(0, count($free) - 1)];
    }

    private static function generateJobId(): string
    {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }
}
