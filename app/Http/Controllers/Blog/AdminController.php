<?php
namespace App\Http\Controllers\Blog;

use App\Blog\Publisher;
use App\Blog\Settings;
use App\Http\Controllers\Controller;
use App\Http\Controllers\frontEnd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class AdminController extends Controller {
    private function db() { return DB::connection('finobe'); }
    private function base(Request $request): array {
        $nav=(new frontEnd($request))->getData();
        return [
            'actor'=>$request->user(),
            'navbarHtml'=>view('v2/Modules/nav',$nav)->render(),
            'blogSettings'=>Settings::all(),
        ];
    }
    public function index(Request $request) {
        $db=$this->db();
        $posts=$db->table('blog_posts')->leftJoin('blog_authors','blog_authors.id','=','blog_posts.author_id')
            ->select('blog_posts.*','blog_authors.name as author_name')
            ->orderByDesc('blog_posts.updated_at')->paginate(20);
        return view('admin.blog.index',$this->base($request)+['posts'=>$posts]);
    }
    public function edit(Request $request, ?string $id=null) {
        $db=$this->db();
        $post=$id?$db->table('blog_posts')->where('id',$id)->first():null;
        if($id) abort_unless($post,404);
        $selected=$post?$db->table('blog_post_tag')->where('post_id',$post->id)->pluck('tag_id')->all():[];
        return view('admin.blog.edit',$this->base($request)+[
            'post'=>$post,'selectedTags'=>$selected,
            'authors'=>$db->table('blog_authors')->orderBy('name')->get(),
            'tags'=>$db->table('blog_tags')->orderBy('name')->get(),
        ]);
    }
    private function validated(Request $request): array {
        return $request->validate([
            'title'=>'required|string|min:2|max:255',
            'slug'=>'nullable|string|max:255',
            'excerpt'=>'nullable|string|max:1000',
            'body'=>'required|string|max:500000',
            'cover_url'=>'nullable|url|max:500',
            'author_id'=>'nullable|integer|min:1',
            'status'=>'required|in:draft,published',
            'tags'=>'nullable|array|max:100',
            'tags.*'=>'integer|min:1',
        ]);
    }
    private function save(Request $request, ?int $id=null) {
        $input=$this->validated($request);
        $db=$this->db();
        if(!empty($input['author_id'])) {
            abort_unless($db->table('blog_authors')->where('id',$input['author_id'])->exists(),422,'Unknown author.');
        }
        $tagIds=array_values(array_unique(array_map('intval',$input['tags']??[])));
        if(count($tagIds)!==$db->table('blog_tags')->whereIn('id',$tagIds)->count()) abort(422,'Unknown blog tag.');
        $existing=$id?$db->table('blog_posts')->where('id',$id)->first():null;
        if($id) abort_unless($existing,404);
        $slug=Publisher::slug((string)(($input['slug'] ?? null) ?: $input['title']),'blog_posts',$id);
        $data=[
            'title'=>$input['title'],'slug'=>$slug,'excerpt'=>$input['excerpt']??null,
            'body'=>$input['body'],'cover_url'=>$input['cover_url']??null,
            'author_id'=>$input['author_id']??null,'status'=>$input['status'],
            'published_at'=>$input['status']==='published' ? (($existing && $existing->status === 'published') ? $existing->published_at : now()) : null,
            'updated_at'=>now(),
        ];
        $db->transaction(function () use ($db,$id,$data,$tagIds,&$postId) {
            if($id) {
                $db->table('blog_posts')->where('id',$id)->update($data);
                $postId=$id;
            } else {
                $postId=$db->table('blog_posts')->insertGetId($data+['created_at'=>now()]);
            }
            $db->table('blog_post_tag')->where('post_id',$postId)->delete();
            foreach($tagIds as $tagId) {
                $db->table('blog_post_tag')->insert(['post_id'=>$postId,'tag_id'=>$tagId]);
            }
        });
        return redirect('/admin/blog/posts/'.$postId.'/edit')->with('admin_success','Post saved.');
    }
    public function store(Request $request) { return $this->save($request); }
    public function update(Request $request, string $id) { return $this->save($request,(int)$id); }
    public function delete(string $id) {
        $db=$this->db();
        $db->transaction(function () use ($db,$id) {
            $db->table('blog_post_tag')->where('post_id',$id)->delete();
            $db->table('blog_posts')->where('id',$id)->delete();
        });
        return redirect('/admin/blog')->with('admin_success','Post deleted.');
    }
    public function authors(Request $request) {
        return view('admin.blog.authors',$this->base($request)+['authors'=>$this->db()->table('blog_authors')->orderBy('name')->get()]);
    }
    public function authorSave(Request $request) {
        $data=$request->validate([
            'id'=>'nullable|integer|min:1','name'=>'required|string|min:2|max:100',
            'bio'=>'nullable|string|max:1500','avatar_url'=>'nullable|url|max:500',
        ]);
        $db=$this->db(); $id=$data['id']??null;
        if($id) abort_unless($db->table('blog_authors')->where('id',$id)->exists(),404);
        $payload=['name'=>$data['name'],'slug'=>Publisher::slug($data['name'],'blog_authors',$id),
            'bio'=>$data['bio']??null,'avatar_url'=>$data['avatar_url']??null,'updated_at'=>now()];
        if($id) $db->table('blog_authors')->where('id',$id)->update($payload);
        else $db->table('blog_authors')->insert($payload+['created_at'=>now()]);
        return redirect('/admin/blog/authors')->with('admin_success','Author saved.');
    }
    public function authorDelete(string $id) {
        $db=$this->db();
        $db->transaction(function () use ($db,$id) {
            $db->table('blog_posts')->where('author_id',$id)->update(['author_id'=>null]);
            $db->table('blog_authors')->where('id',$id)->delete();
        });
        return redirect('/admin/blog/authors')->with('admin_success','Author deleted.');
    }
    public function tags(Request $request) {
        return view('admin.blog.tags',$this->base($request)+['tags'=>$this->db()->table('blog_tags')->orderBy('name')->get()]);
    }
    public function tagSave(Request $request) {
        $data=$request->validate(['id'=>'nullable|integer|min:1','name'=>'required|string|min:2|max:100',
            'description'=>'nullable|string|max:1500']);
        $db=$this->db(); $id=$data['id']??null;
        if($id) abort_unless($db->table('blog_tags')->where('id',$id)->exists(),404);
        $payload=['name'=>$data['name'],'slug'=>Publisher::slug($data['name'],'blog_tags',$id),
            'description'=>$data['description']??null,'updated_at'=>now()];
        if($id) $db->table('blog_tags')->where('id',$id)->update($payload);
        else $db->table('blog_tags')->insert($payload+['created_at'=>now()]);
        return redirect('/admin/blog/tags')->with('admin_success','Tag saved.');
    }
    public function tagDelete(string $id) {
        $db=$this->db();
        $db->transaction(function () use ($db,$id) {
            $db->table('blog_post_tag')->where('tag_id',$id)->delete();
            $db->table('blog_tags')->where('id',$id)->delete();
        });
        return redirect('/admin/blog/tags')->with('admin_success','Tag deleted.');
    }
    public function settings(Request $request) {
        return view('admin.blog.settings',$this->base($request));
    }
    public function settingsSave(Request $request) {
        $data=$request->validate([
            'site_title'=>'required|string|min:2|max:140',
            'site_description'=>'nullable|string|max:500',
            'logo_url'=>'nullable|url|max:500',
            'cover_url'=>'nullable|url|max:500',
            'social_url'=>'nullable|url|max:500',
            'accent_color'=>'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);
        Settings::save($data);
        return redirect('/admin/blog/settings')->with('admin_success','Blog settings updated.');
    }
}
