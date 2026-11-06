<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/*
    PlayCors
     - lets the web client on https://play.<domain> read responses from the site's other hosts
       (placelauncher, join script, assets, character appearance, ...)
*/
class PlayCors
{
    public function handle(Request $request, Closure $next)
    {
        $origin = 'https://play.' . env('FINOBE_DOMAIN', 'finobe.wtf');
        if ($request->headers->get('Origin') !== $origin) {
            return $next($request);
        }

        $headers = [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => (string) $request->headers->get('Access-Control-Request-Headers', 'Content-Type'),
            'Access-Control-Max-Age' => '600',
            'Cross-Origin-Resource-Policy' => 'cross-origin',
            'Vary' => 'Origin',
        ];

        if ($request->isMethod('OPTIONS')) {
            return response('', 204, $headers);
        }

        $response = $next($request);
        foreach ($headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        return $response;
    }
}
