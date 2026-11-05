<?php
namespace App\Http\Controllers\Blog;

use App\Blog\Publisher;
use App\Blog\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PublicController extends Controller {
    private function base(): array {
        return ['settings'=>Settings::all(),'blogUrl'=>config('blog.url')];
    }
    private function query() {
        return Publisher::publishable()
            ->leftJoin('blog_authors','blog_authors.id','=','blog_posts.author_id')
            ->select('blog_posts.*','blog_authors.name as author_name','blog_authors.slug as author_slug');
    }
    public function index(Request $request) {
        $posts=$this->query()->orderByDesc('blog_posts.published_at')->paginate(10);
        return view('blog.index',$this->base()+['posts'=>$posts,'heading'=>'Latest stories','description'=>null]);
    }
    public function tag(string $slug) {
        $db=DB::connection('finobe');
        $tag=$db->table('blog_tags')->where('slug',$slug)->first();
        abort_unless($tag,404);
        $posts=$this->query()->whereExists(function ($q) use ($tag) {
            $q->selectRaw('1')->from('blog_post_tag')
                ->whereColumn('blog_post_tag.post_id','blog_posts.id')
                ->where('blog_post_tag.tag_id',$tag->id);
        })->orderByDesc('blog_posts.published_at')->paginate(10);
        return view('blog.index',$this->base()+['posts'=>$posts,'heading'=>$tag->name,'description'=>$tag->description]);
    }
    public function author(string $slug) {
        $author=DB::connection('finobe')->table('blog_authors')->where('slug',$slug)->first();
        abort_unless($author,404);
        $posts=$this->query()->where('blog_posts.author_id',$author->id)
            ->orderByDesc('blog_posts.published_at')->paginate(10);
        return view('blog.index',$this->base()+['posts'=>$posts,'heading'=>$author->name,'description'=>$author->bio]);
    }
    public function show(string $slug) {
        $post=$this->query()->where('blog_posts.slug',$slug)->first();
        abort_unless($post,404);
        $post->body_html=Publisher::html((string)$post->body);
        $tags=DB::connection('finobe')->table('blog_tags')
            ->join('blog_post_tag','blog_tags.id','=','blog_post_tag.tag_id')
            ->where('blog_post_tag.post_id',$post->id)
            ->orderBy('blog_tags.name')->get(['blog_tags.name','blog_tags.slug']);
        return view('blog.post',$this->base()+['post'=>$post,'tags'=>$tags]);
    }
}
