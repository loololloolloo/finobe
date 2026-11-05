<?php
namespace App\Blog;

use Illuminate\Database\Eloquent\Model;

final class Post extends Model {
    protected $connection='finobe';
    protected $table='blog_posts';
    protected $guarded=['id'];
    protected function casts(): array {
        return ['published_at'=>'datetime'];
    }
    public function author() { return $this->belongsTo(Author::class,'author_id'); }
    public function tags() { return $this->belongsToMany(Tag::class,'blog_post_tag','post_id','tag_id'); }
}
