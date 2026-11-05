<?php

return [
    // Filesystem root of the CDN (cdn.<domain>) web root on the server.
    'cdn_path' => rtrim(env('FINOBE_CDN_PATH', '/var/www/cdn.finobe.net'), '/'),
];
