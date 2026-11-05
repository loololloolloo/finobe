<?php

namespace App\Http\Controllers\Blog;

use App\Blog\Publisher;
use App\Blog\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PublicController extends Controller
{
    private function base(): array
    {
        return ['settings' => Settings::all(), 'blogUrl' => config('blog.url')];
    }

    private function query()
    {
        return Publisher::publishable()
            ->leftJoin('blog_authors', 'blog_authors.id', '=', 'blog_posts.author_id')
            ->select(
                'blog_posts.*',
                'blog_authors.name as author_name',
                'blog_authors.slug as author_slug',
                'blog_authors.avatar_url as author_avatar'
            )
            ->selectSub(function ($q) {
                $q->from('blog_tags')
                    ->join('blog_post_tag', 'blog_post_tag.tag_id', '=', 'blog_tags.id')
                    ->whereColumn('blog_post_tag.post_id', 'blog_posts.id')
                    ->orderBy('blog_tags.name')
                    ->limit(1)
                    ->select('blog_tags.name');
            }, 'primary_tag_name')
            ->selectSub(function ($q) {
                $q->from('blog_tags')
                    ->join('blog_post_tag', 'blog_post_tag.tag_id', '=', 'blog_tags.id')
                    ->whereColumn('blog_post_tag.post_id', 'blog_posts.id')
                    ->orderBy('blog_tags.name')
                    ->limit(1)
                    ->select('blog_tags.slug');
            }, 'primary_tag_slug');
    }

    private function page(Request $request): int
    {
        $page = (int) ($request->route('page') ?: $request->query('page', 1));
        abort_unless($page >= 1 && $page <= 100000, 404);
        return $page;
    }

    private function listPage($query, Request $request, string $heading, ?string $description, string $kind, $profile = null)
    {
        $page = $this->page($request);
        $posts = $query->orderByDesc('blog_posts.published_at')->orderByDesc('blog_posts.id')
            ->paginate(12, ['*'], 'page', $page);

        if ($kind === 'home') {
            $pageBase = '/';
        } elseif ($kind === 'author') {
            $pageBase = '/author/'.$profile->slug.'/';
        } elseif ($kind === 'tag') {
            $pageBase = '/tag/'.$profile->slug.'/';
        } else {
            $pageBase = '/search/';
            $posts->appends(['q' => (string)$request->query('q', '')]);
        }

        return view('blog.index', $this->base() + [
            'posts' => $posts,
            'heading' => $heading,
            'description' => $description,
            'kind' => $kind,
            'profile' => $profile,
            'pageBase' => $pageBase,
            'pageNumber' => $page,
        ]);
    }

    public function index(Request $request)
    {
        return $this->listPage($this->query(), $request, 'Latest stories', null, 'home');
    }

    public function tag(Request $request, string $slug)
    {
        $tag = DB::connection('finobe')->table('blog_tags')->where('slug', $slug)->first();
        abort_unless($tag, 404);

        $query = $this->query()->whereExists(function ($q) use ($tag) {
            $q->selectRaw('1')->from('blog_post_tag')
                ->whereColumn('blog_post_tag.post_id', 'blog_posts.id')
                ->where('blog_post_tag.tag_id', $tag->id);
        });

        return $this->listPage($query, $request, $tag->name, $tag->description, 'tag', $tag);
    }

    public function author(Request $request, string $slug)
    {
        $author = DB::connection('finobe')->table('blog_authors')->where('slug', $slug)->first();
        abort_unless($author, 404);

        return $this->listPage(
            $this->query()->where('blog_posts.author_id', $author->id),
            $request, $author->name, $author->bio, 'author', $author
        );
    }

    public function search(Request $request)
    {
        $data = $request->validate(['q' => 'nullable|string|max:120']);
        $term = trim((string)($data['q'] ?? ''));
        $query = $this->query();
        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('blog_posts.title', 'like', $like)
                    ->orWhere('blog_posts.excerpt', 'like', $like)
                    ->orWhere('blog_posts.body', 'like', $like);
            });
        }

        return $this->listPage($query, $request, 'Search results',
            $term === '' ? 'All published posts' : 'Results for: '.$term, 'search');
    }

    public function show(string $slug)
    {
        return $this->renderPost($this->query()->where('blog_posts.slug', $slug)->first());
    }

    public function showDated(string $year, string $month, string $day, string $slug)
    {
        abort_unless(checkdate((int)$month, (int)$day, (int)$year), 404);
        $date = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);
        $post = $this->query()
            ->where('blog_posts.slug', $slug)
            ->whereDate('blog_posts.published_at', $date)
            ->first();

        return $this->renderPost($post);
    }

    private function renderPost($post)
    {
        abort_unless($post, 404);
        $post->body_html = Publisher::html((string)$post->body);

        $tags = DB::connection('finobe')->table('blog_tags')
            ->join('blog_post_tag', 'blog_tags.id', '=', 'blog_post_tag.tag_id')
            ->where('blog_post_tag.post_id', $post->id)
            ->orderBy('blog_tags.name')
            ->get(['blog_tags.name', 'blog_tags.slug']);

        $prev = $this->query()
            ->where('blog_posts.published_at', '<', $post->published_at)
            ->orderByDesc('blog_posts.published_at')->first();
        $next = $this->query()
            ->where('blog_posts.published_at', '>', $post->published_at)
            ->orderBy('blog_posts.published_at')->first();

        return view('blog.post', $this->base() + [
            'post' => $post, 'tags' => $tags, 'prev' => $prev, 'next' => $next,
        ]);
    }

    public function rss()
    {
        $posts = $this->query()->orderByDesc('blog_posts.published_at')->limit(20)->get();
        return response()->view('blog.rss', $this->base() + ['posts' => $posts])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=300');
    }
}
