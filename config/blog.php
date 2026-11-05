<?php
return [
    'url' => rtrim(env('BLOG_URL', 'https://blog.finobe.wtf'), '/'),
    'name' => env('BLOG_NAME', 'Finobe Blog'),
    'description' => env('BLOG_DESCRIPTION', 'News, updates, and stories from Finobe.'),
];
