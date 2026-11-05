@extends('blog.layout')
@section('title', $heading === 'Latest stories' ? $settings['site_title'] : $heading.' - '.$settings['site_title'])
@section('description', $description ?: $settings['site_description'])
@section('content')
<section class="blog-heading">
  <h1>{{ $heading }}</h1>
  @if($description)<p>{{ $description }}</p>@endif
</section>
<div class="blog-feed">
@forelse($posts as $post)
<article class="blog-story">
  @if($post->cover_url)<a class="blog-story-cover" href="/{{ $post->slug }}"><img src="{{ $post->cover_url }}" alt="" loading="lazy"></a>@endif
  <div class="blog-story-content">
    <div class="blog-story-date">{{ \Carbon\Carbon::parse($post->published_at)->format('M j, Y') }}</div>
    <h2><a href="/{{ $post->slug }}">{{ $post->title }}</a></h2>
    @if($post->excerpt)<p>{{ $post->excerpt }}</p>@else<p>{{ \Illuminate\Support\Str::limit(strip_tags(\App\Blog\Publisher::html($post->body)), 180) }}</p>@endif
    <div class="blog-story-byline">@if($post->author_slug)<a href="/author/{{ $post->author_slug }}">{{ $post->author_name }}</a>@elseFinobe Team@endif</div>
  </div>
</article>
@empty
<p class="blog-empty">No published posts yet.</p>
@endforelse
</div>
@if($posts->hasPages())
<div class="blog-pagination">
  @if($posts->onFirstPage()) <span>Previous</span> @else <a href="{{ $posts->previousPageUrl() }}">Previous</a> @endif
  <span>Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
  @if($posts->hasMorePages()) <a href="{{ $posts->nextPageUrl() }}">Next</a> @else <span>Next</span> @endif
</div>
@endif
@endsection
