<?php
return [
    'owner_ids' => array_filter(array_map('intval', explode(',', env('ADMIN_OWNER_IDS', '')))),
    'cdn_path' => env('FINOBE_CDN_PATH', '/var/www/cdn.'.env('FINOBE_DOMAIN', 'finobe.wtf')),
];
