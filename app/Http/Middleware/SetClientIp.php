<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetClientIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only trust Cloudflare's header when it holds a valid IP; otherwise keep the real peer address.
        $cfIp = $request->server->get('HTTP_CF_CONNECTING_IP');

        if (is_string($cfIp) && filter_var($cfIp, FILTER_VALIDATE_IP)) {
            $request->server->set('REMOTE_ADDR', $cfIp);
        }

        return $next($request);
    }
}
