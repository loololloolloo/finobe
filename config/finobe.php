<?php

return [
    // Filesystem root of the CDN (cdn.<domain>) web root on the server.
    'cdn_path' => rtrim(env('FINOBE_CDN_PATH', '/var/www/cdn.finobe.net'), '/'),

    // The Windows machine running the patched RCCService (see tools/rcc/README.md).
    'arbiter' => [
        // address the web server uses to reach RCC's SOAP port (keep this private/firewalled)
        'host' => env('FINOBE_ARBITER_HOST', '127.0.0.1'),
        'port' => (int) env('FINOBE_ARBITER_PORT', 64989),
    ],

    // Where players connect for game servers (public IP of the RCC machine) and the UDP ports RCC may use.
    'gameserver' => [
        'host' => env('FINOBE_GAMESERVER_HOST', env('FINOBE_ARBITER_HOST', '127.0.0.1')),
        'port_min' => (int) env('FINOBE_GAMESERVER_PORT_MIN', 53640),
        'port_max' => (int) env('FINOBE_GAMESERVER_PORT_MAX', 53740),
    ],
];
