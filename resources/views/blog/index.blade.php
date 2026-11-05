@extends('blog.layout')
@section('body-class', $kind === 'home' ? 'home-template' : ($kind === 'author' ? 'author-template' : 'tag-template'))
@section('header-class', $kind === 'home' ? 'site-header-home' : 'site-header-archive')
@section('title', e($kind === 'home' ? $settings['site_title'] : $heading.' - '.$settings['site_title']))
@section('description', e($description ?: $settings['site_description']))
@section('hero')
    <div class="site-header-content">
        @if($kind === 'home')
            <h1 class="site-title">{{ $settings['site_title'] }}</h1>
            @if($settings['site_description'])<h2 class="site-description">{{ $settings['site_description'] }}</h2>@endif
        @elseif($kind === 'author')
            @if($profile && $profile->avatar_url)
                <img class="site-profile-image" src="{{ $profile->avatar_url }}" alt="{{ $heading }}">
            @endif
            <h1 class="site-title">{{ $heading }}</h1>
            @if($description)<h2 class="site-description">{{ $description }}</h2>@endif
        @else
            <div class="site-header-kicker">A collection of posts</div>
            <h1 class="site-title">{{ $heading }}</h1>
            @if($description)<h2 class="site-description">{{ $description }}</h2>@endif
        @endif
    </div>
@endsection
@section('content')
<section class="post-feed {{ $kind === 'home' ? 'post-feed-home' : 'post-feed-archive' }}" aria-label="Published stories">
    @forelse($posts as $post)
        <article class="post-card {{ $kind === 'home' && ($loop->iteration % 6 === 1) ? 'post-card-large' : '' }} {{ !$post->cover_url ? 'no-image' : '' }}">
            @if($post->cover_url)
                <a class="post-card-image-link" href="/{{ $post->slug }}/" aria-label="Read {{ $post->title }}">
                    <img class="post-card-image" src="{{ $post->cover_url }}" alt="" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                </a>
            @endif
            <div class="post-card-content">
                <a class="post-card-content-link" href="/{{ $post->slug }}/">
                    <header class="post-card-header">
                        @if($post->primary_tag_name)
                            <span class="post-card-primary-tag">{{ $post->primary_tag_name }}</span>
                        @endif
                        <h2 class="post-card-title">{{ $post->title }}</h2>
                    </header>
                    <section class="post-card-excerpt">
                        <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html($post->body)), 190) }}</p>
                    </section>
                </a>
                <footer class="post-card-meta">
                    @if($post->author_avatar)
                        <img class="author-profile-image" src="{{ $post->author_avatar }}" width="34" height="34" loading="lazy" alt="">
                    @else
                        <span class="author-profile-placeholder" aria-hidden="true">F</span>
                    @endif
                    <div class="post-card-meta-details">
                        @if($post->author_slug)
                            <a href="/author/{{ $post->author_slug }}/" class="post-card-author">{{ $post->author_name }}</a>
                        @else
                            <span class="post-card-author">Finobe Team</span>
                        @endif
                        <div class="post-card-meta-secondary">
                            <time datetime="{{ \Carbon\Carbon::parse($post->published_at)->toDateString() }}">{{ \Carbon\Carbon::parse($post->published_at)->format('j M Y') }}</time>
                            <span class="meta-divider" aria-hidden="true">·</span>
                            <span>{{ max(1, (int) ceil(str_word_count((string) $post->body) / 200)) }} min read</span>
                        </div>
                    </div>
                </footer>
            </div>
        </article>
    @empty
        <div class="post-feed-empty">
            <h2>No posts yet</h2>
            <p>Published news and updates will appear here.</p>
        </div>
    @endforelse
</section>
@if($posts->hasPages())
    <nav class="pagination" aria-label="Post pagination">
        @if(!$posts->onFirstPage())
            <a class="newer-posts" href="{{ $posts->currentPage() === 2 ? $pageBase : $pageBase.'page/'.($posts->currentPage() - 1).'/' }}" rel="prev">← Newer Posts</a>
        @else
            <span class="pagination-spacer"></span>
        @endif
        <span class="page-number">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
        @if($posts->hasMorePages())
            <a class="older-posts" href="{{ $pageBase.'page/'.($posts->currentPage() + 1).'/' }}" rel="next">Older Posts →</a>
        @else
            <span class="pagination-spacer"></span>
        @endif
    </nav>
@endif
@endsection
