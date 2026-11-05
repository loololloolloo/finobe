<?php
namespace App\Blog;

use Illuminate\Support\Facades\DB;

final class Settings {
    public static function all(): array {
        $rows = DB::connection('finobe')->table('blog_settings')->pluck('value', 'key')->all();
        return array_merge([
            'site_title' => config('blog.name', 'Finobe Blog'),
            'site_description' => config('blog.description', ''),
            'logo_url' => '',
            'cover_url' => '',
            'social_url' => '',
            'accent_color' => '#202020',
        ], $rows);
    }
    public static function save(array $data): void {
        $db = DB::connection('finobe');
        foreach ($data as $key=>$value) {
            if (!in_array($key,['site_title','site_description','logo_url','cover_url','social_url','accent_color'],true)) continue;
            $db->table('blog_settings')->updateOrInsert(['key'=>$key],['value'=>(string)($value ?? '')]);
        }
    }
}
