@extends('blog.layout')
@section('body-class', 'post-template')
@section('header-class', 'site-header-post')
@section('title', e($post->title.' - '.$settings['site_title']))
@section('description', e($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body_html), 160)))
@section('og-type','article')
@section('meta')
    <meta property="article:published_time" content="{{ \Carbon\Carbon::parse($post->published_at)->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ \Carbon\Carbon::parse($post->updated_at)->toIso8601String() }}">
    @if($post->cover_url)<meta property="og:image" content="{{ $post->cover_url }}">@endif
@endsection
@section('content')
<article class="post-full">
    <header class="post-full-header">
        @if($post->primary_tag_slug)
            <a class="post-full-kicker" href="/tag/{{ $post->primary_tag_slug }}/">{{ $post->primary_tag_name }}</a>
        @endif
        <h1 class="post-full-title">{{ $post->title }}</h1>
        @if($post->excerpt)<p class="post-full-description">{{ $post->excerpt }}</p>@endif
        <div class="post-full-byline">
            @if($post->author_avatar)
                <img class="post-full-author-image" src="{{ $post->author_avatar }}" alt="" width="40" height="40">
            @else
                <span class="post-full-author-image post-full-author-placeholder" aria-hidden="true">F</span>
            @endif
            <div class="post-full-byline-content">
                @if($post->author_slug)
                    <a class="post-full-author-link" href="/author/{{ $post->author_slug }}/">{{ $post->author_name }}</a>
                @else
                    <span class="post-full-author-link">Finobe Team</span>
                @endif
                <div class="post-full-byline-meta">
                    <time datetime="{{ \Carbon\Carbon::parse($post->published_at)->toDateString() }}">{{ \Carbon\Carbon::parse($post->published_at)->format('j M Y') }}</time>
                    <span aria-hidden="true">·</span>
                    <span>{{ max(1, (int) ceil(str_word_count((string)$post->body) / 200)) }} min read</span>
                </div>
            </div>
        </div>
    </header>

    @if($post->cover_url)
        <figure class="post-full-image">
            <img src="{{ $post->cover_url }}" alt="">
        </figure>
    @endif

    <section class="post-full-content">
        <div class="post-content">{!! $post->body_html !!}</div>
    </section>

    <footer class="post-full-footer">
        @if($tags->isNotEmpty())
            <div class="post-full-tags" aria-label="Tags">
                @foreach($tags as $tag)<a href="/tag/{{ $tag->slug }}/">{{ $tag->name }}</a>@endforeach
            </div>
        @endif
        <p class="post-full-footer-back"><a href="/">← Back to all posts</a></p>
    </footer>
</article>

@if($prev || $next)
<aside class="read-next" aria-label="More stories">
    <div class="read-next-inner">
        @if($prev)
            <a class="read-next-story" href="/{{ $prev->slug }}/" @if($prev->cover_url) style="background-image:linear-gradient(rgba(0,0,0,.45),rgba(0,0,0,.73)),url('{{ $prev->cover_url }}')" @endif>
                <span class="read-next-kicker">Previous story</span>
                <h3>{{ $prev->title }}</h3>
                <p>{{ $prev->excerpt ?: \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html($prev->body)), 100) }}</p>
            </a>
        @endif
        @if($next)
            <a class="read-next-story" href="/{{ $next->slug }}/" @if($next->cover_url) style="background-image:linear-gradient(rgba(0,0,0,.45),rgba(0,0,0,.73)),url('{{ $next->cover_url }}')" @endif>
                <span class="read-next-kicker">Next story</span>
                <h3>{{ $next->title }}</h3>
                <p>{{ $next->excerpt ?: \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html($next->body)), 100) }}</p>
            </a>
        @endif
    </div>
</aside>
@endif
@endsection
