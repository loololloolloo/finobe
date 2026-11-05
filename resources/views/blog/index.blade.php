@extends('blog.layout')
@section('body-class', $kind === 'home' ? 'home-template' : ($kind === 'author' ? 'author-template' : ($kind === 'tag' ? 'tag-template' : 'search-template')))
@section('cover-class', $kind === 'home' ? 'has-cover' : '')
@section('title', e($kind === 'home' ? $settings['site_title'] : ($kind === 'search' ? 'Search - '.$settings['site_title'] : $heading.' - '.$settings['site_title'])))
@section('description', e($description ?: $settings['site_description']))
@section('hero')
    @if($kind === 'home')
        <div class="site-header-content outer">
            <img class="site-header-cover" src="{{ $settings['cover_url'] ?: '/s/img/bannerlate2019-2.jpg' }}" alt="{{ $settings['site_title'] }}" fetchpriority="high">
            <div class="site-header-inner inner">
                <img class="site-logo" src="{{ $settings['logo_url'] ?: '/s/img/finnobe3logo.png' }}" alt="{{ $settings['site_title'] }}">
                <p class="site-description">{{ $settings['site_description'] }}</p>
            </div>
        </div>
    @else
        <div class="blog-archive-heading outer">
            <div class="inner">
                @if($kind === 'search')
                    <h1>Search results</h1>
                    @if($description)<p>{{ $description }}</p>@endif
                @elseif($kind === 'tag')
                    <p class="blog-archive-kicker">A collection of posts</p>
                    <h1>{{ $heading }}</h1>
                    @if($description)<p>{{ $description }}</p>@endif
                @else
                    @if($profile && $profile->avatar_url)
                        <img class="blog-profile-avatar" src="{{ $profile->avatar_url }}" alt="">
                    @endif
                    <h1>{{ $heading }}</h1>
                    @if($description)<p>{{ $description }}</p>@endif
                @endif
            </div>
        </div>
    @endif
@endsection
@section('content')
<main id="site-main" class="site-main outer">
    <div class="inner posts">
        <div class="post-feed">
            @forelse($posts as $post)
                <article class="post-card post {{ $post->cover_url ? '' : 'no-image' }} keep-ratio">
                    @if($post->cover_url)
                        <a class="post-card-image-link" href="/{{ \Carbon\Carbon::parse($post->published_at)->format('Y/m/d') }}/{{ $post->slug }}/">
                            <img class="post-card-image" src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="lazy">
                        </a>
                    @endif
                    <div class="post-card-content">
                        <a class="post-card-content-link" href="/{{ \Carbon\Carbon::parse($post->published_at)->format('Y/m/d') }}/{{ $post->slug }}/">
                            <header class="post-card-header">
                                <div class="post-card-tags">
                                    @if($post->primary_tag_name)
                                        <span class="post-card-primary-tag">{{ $post->primary_tag_name }}</span>
                                    @endif
                                </div>
                                <h2 class="post-card-title">{{ $post->title }}</h2>
                            </header>
                            <div class="post-card-excerpt">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html((string)$post->body)), 240) }}</div>
                        </a>
                        <footer class="post-card-meta">
                            <time class="post-card-meta-date" datetime="{{ \Carbon\Carbon::parse($post->published_at)->toDateString() }}">{{ \Carbon\Carbon::parse($post->published_at)->format('d M Y') }}</time>
                            @if(trim((string)$post->body) !== '')
                                <span class="post-card-meta-length">{{ max(1, (int) ceil(str_word_count((string)$post->body) / 275)) }} min read</span>
                            @endif
                        </footer>
                    </div>
                </article>
            @empty
                <div class="blog-empty-state">
                    <h2>No posts found</h2>
                    <p>{{ $kind === 'search' ? 'Try a different search.' : 'Published news and updates will appear here.' }}</p>
                </div>
            @endforelse
        </div>
        <nav class="pagination" aria-label="Post pagination">
            @if(!$posts->onFirstPage())
                <a class="newer-posts" href="{{ $kind === 'search' ? $posts->previousPageUrl() : ($posts->currentPage() === 2 ? $pageBase : $pageBase.'page/'.($posts->currentPage() - 1).'/') }}" rel="prev">← Newer Posts</a>
            @endif
            <span class="page-number">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
            @if($posts->hasMorePages())
                <a class="older-posts" href="{{ $kind === 'search' ? $posts->nextPageUrl() : $pageBase.'page/'.($posts->currentPage() + 1).'/' }}" rel="next">Older Posts →</a>
            @endif
        </nav>
    </div>
</main>
@endsection
