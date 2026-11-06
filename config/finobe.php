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

    /*
        The clients a place can be made for (the "version" in a place's settings). Each RCC-based year runs
        its own RCCService (same SOAP API as 2016) and can live on its own machine; leave the host/port empty
        to use the 2016 one's machine with the default port. 'enabled' hides a year from the site until its
        client and game servers are set up.
          server: rcc      = RCCService job, script storage/rbx/files/scripts/<script>
                  host2012 = the 2012 client hosts (App\Services\Host2012)
          join:   json     = JSON join script signed --rbxsig (2016+)
                  lua2012  = storage/rbx/files/scripts/join2012.lua signed V1
          Every year is installed and started by FinobeLauncher (finobe-launcher://...+clientversion:<year>):
          publish a year's build with tools/client/package_client.py --year <year> and set
          FINOBE_CLIENT_VERSION_<year> (2016: FINOBE_CLIENT_VERSION).
    */
    'clients' => [
        '2012' => [
            'enabled' => true,
            'server' => 'host2012',
            'join' => 'lua2012',
            'scheme' => 'http',
        ],
        '2016' => [
            'enabled' => true,
            'server' => 'rcc',
            'script' => 'gameserver2016.lua',
            'join' => 'json',
            'scheme' => 'https',
            'rcc_host' => env('FINOBE_ARBITER_HOST', '127.0.0.1'),
            'rcc_port' => (int) env('FINOBE_ARBITER_PORT', 64989),
            'gameserver_host' => env('FINOBE_GAMESERVER_HOST', env('FINOBE_ARBITER_HOST', '127.0.0.1')),
        ],
    ],
];
