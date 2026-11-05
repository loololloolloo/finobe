@extends('blog.layout')
@section('body-class', 'post-template')
@section('title', e($post->title.' - '.$settings['site_title']))
@section('description', e($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body_html), 160)))
@section('og-type', 'article')
@section('meta')
<meta property="article:published_time" content="{{ \Carbon\Carbon::parse($post->published_at)->toIso8601String() }}">
<meta property="article:modified_time" content="{{ \Carbon\Carbon::parse($post->updated_at)->toIso8601String() }}">
@if($post->cover_url)<meta property="og:image" content="{{ $post->cover_url }}">@endif
@endsection
@section('content')
<main id="site-main" class="site-main">
    <article class="article post">
        <header class="article-header gh-canvas">
            <div class="article-tag post-card-tags">
                @if($post->primary_tag_slug)
                    <span class="post-card-primary-tag"><a href="/tag/{{ $post->primary_tag_slug }}/">{{ $post->primary_tag_name }}</a></span>
                @endif
            </div>
            <h1 class="article-title">{{ $post->title }}</h1>
            @if($post->excerpt)
                <p class="article-excerpt">{{ $post->excerpt }}</p>
            @endif

            <div class="article-byline">
                <section class="article-byline-content">
                    <ul class="author-list">
                        <li class="author-list-item">
                            @if($post->author_avatar)
                                <a class="author-avatar" href="/author/{{ $post->author_slug }}/">
                                    <img class="author-profile-image" src="{{ $post->author_avatar }}" alt="{{ $post->author_name }}">
                                </a>
                            @else
                                <span class="author-avatar author-profile-image blog-author-default" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8z"/></svg>
                                </span>
                            @endif
                        </li>
                    </ul>
                    <div class="article-byline-meta">
                        <h4 class="author-name">
                            @if($post->author_slug)<a href="/author/{{ $post->author_slug }}/">{{ $post->author_name }}</a>
                            @else Finobe Team @endif
                        </h4>
                        <div class="byline-meta-content">
                            <time class="byline-meta-date" datetime="{{ \Carbon\Carbon::parse($post->published_at)->toDateString() }}">{{ \Carbon\Carbon::parse($post->published_at)->format('d M Y') }}</time>
                            <span class="byline-reading-time"><span class="bull">&bull;</span> {{ max(1, (int) ceil(str_word_count((string)$post->body) / 275)) }} min read</span>
                        </div>
                    </div>
                </section>
            </div>

            @if($post->cover_url)
                <figure class="article-image">
                    <img class="gh-feature-image" src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="eager">
                </figure>
            @endif
        </header>

        <section class="gh-content gh-canvas">
            {!! $post->body_html !!}
        </section>
        @if($tags->isNotEmpty())
            <footer class="article-footer gh-canvas">
                <div class="blog-post-tags">
                    @foreach($tags as $tag)<a href="/tag/{{ $tag->slug }}/">{{ $tag->name }}</a>@endforeach
                </div>
            </footer>
        @endif
    </article>
</main>
@if($prev || $next)
    <aside class="read-more-wrap outer">
        <div class="read-more inner">
            @foreach([$prev, $next] as $more)
                @if($more)
                    <article class="post-card post {{ $more->cover_url ? '' : 'no-image' }}">
                        @if($more->cover_url)
                            <a class="post-card-image-link" href="/{{ \Carbon\Carbon::parse($more->published_at)->format('Y/m/d') }}/{{ $more->slug }}/"><img class="post-card-image" src="{{ $more->cover_url }}" alt="" loading="lazy"></a>
                        @endif
                        <div class="post-card-content">
                            <a class="post-card-content-link" href="/{{ \Carbon\Carbon::parse($more->published_at)->format('Y/m/d') }}/{{ $more->slug }}/">
                                <header class="post-card-header"><h2 class="post-card-title">{{ $more->title }}</h2></header>
                                <div class="post-card-excerpt">{{ $more->excerpt ?: \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html((string)$more->body)), 190) }}</div>
                            </a>
                            <footer class="post-card-meta"><time class="post-card-meta-date" datetime="{{ \Carbon\Carbon::parse($more->published_at)->toDateString() }}">{{ \Carbon\Carbon::parse($more->published_at)->format('d M Y') }}</time></footer>
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    </aside>
@endif
@endsection
