@extends('blog.layout')
@section('title', $post->title.' - '.$settings['site_title'])
@section('description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body_html), 160))
@section('meta')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->title }}">
@if($post->cover_url)<meta property="og:image" content="{{ $post->cover_url }}">@endif
@endsection
@section('content')
<article class="blog-article">
  <a class="blog-return" href="/">← Back to blog</a>
  <header>
    <p class="blog-story-date">{{ \Carbon\Carbon::parse($post->published_at)->format('F j, Y') }}</p>
    <h1>{{ $post->title }}</h1>
    @if($post->excerpt)<p class="blog-deck">{{ $post->excerpt }}</p>@endif
    <div class="blog-author">@if($post->author_slug)<a href="/author/{{ $post->author_slug }}">{{ $post->author_name }}</a>@elseFinobe Team@endif</div>
  </header>
  @if($post->cover_url)<img class="blog-feature-image" src="{{ $post->cover_url }}" alt="">@endif
  <div class="blog-article-body">{!! $post->body_html !!}</div>
  @if($tags->isNotEmpty())
  <div class="blog-tags">@foreach($tags as $tag)<a href="/tag/{{ $tag->slug }}">{{ $tag->name }}</a>@endforeach</div>
  @endif
</article>
@endsection
