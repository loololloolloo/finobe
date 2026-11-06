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

    // 2012 game servers: there's no 2012 RCC, so tools/gameserver2012/host2012.ps1 on the game server machine
    // runs the 2012 client as the server. Address the web server uses to reach it (keep it private/firewalled).
    'host2012' => [
        'url' => rtrim((string) env('FINOBE_HOST2012_URL', ''), '/'),   // e.g. http://10.0.0.5:64990
    ],

    // the 2012 client zip players download (the client2012 branch's Client2012 folder, zipped)
    'client2012_url' => env('FINOBE_CLIENT2012_URL') ?: 'https://prod1-setup.' . env('FINOBE_DOMAIN', 'finobe.wtf') . '/Finobe2012.zip',
];
