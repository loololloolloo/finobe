<?php
namespace App\Blog;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class Publisher {
    public static function slug(string $text, string $table, ?int $except = null): string {
        $stem = substr(Str::slug($text),0,110) ?: 'entry';
        $value = $stem;
        for ($n=2; $n<100000; $n++) {
            $query = DB::connection('finobe')->table($table)->where('slug',$value);
            if ($except !== null) $query->where('id','!=',$except);
            if (!$query->exists()) return $value;
            $value = $stem.'-'.$n;
        }
        abort(422,'Could not create a unique slug.');
    }
    public static function html(string $markdown): string {
        return Str::markdown($markdown,['html_input'=>'strip','allow_unsafe_links'=>false]);
    }
    public static function publishable() {
        return DB::connection('finobe')->table('blog_posts')
            ->where('status','published')
            ->whereNotNull('published_at')
            ->where('published_at','<=',now());
    }
}
