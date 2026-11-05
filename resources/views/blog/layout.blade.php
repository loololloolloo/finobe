<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', $settings['site_title'])</title>
<meta name="description" content="@yield('description', $settings['site_description'])">
<link rel="icon" href="/s/img/finnobe3logo.png">
<link rel="stylesheet" href="/s/blog/blog.css">
@yield('meta')
</head>
<body style="--blog-accent:{{ $settings['accent_color'] }}">
<header class="blog-header" @if($settings['cover_url']) style="background-image:linear-gradient(rgba(0,0,0,.55),rgba(0,0,0,.55)),url('{{ $settings['cover_url'] }}')" @endif>
  <div class="blog-wrap">
    <nav class="blog-topbar" aria-label="Blog navigation">
      <a href="/" class="blog-brand">@if($settings['logo_url'])<img src="{{ $settings['logo_url'] }}" alt="{{ $settings['site_title'] }}">@else{{ $settings['site_title'] }}@endif</a>
      <div><a href="/">Home</a><a href="{{ config('app.url') }}">Finobe</a></div>
    </nav>
    <div class="blog-hero">
      <a href="/" class="blog-hero-title">{{ $settings['site_title'] }}</a>
      <p>{{ $settings['site_description'] }}</p>
    </div>
  </div>
</header>
<main class="blog-wrap blog-main">@yield('content')</main>
<footer class="blog-footer">
  <div class="blog-wrap"><span>&copy; {{ date('Y') }} {{ $settings['site_title'] }}</span>
  <span><a href="/">Home</a>@if($settings['social_url']) <a href="{{ $settings['social_url'] }}" rel="noopener noreferrer">Community</a>@endif</span></div>
</footer>
</body></html>
