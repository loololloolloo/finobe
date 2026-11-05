<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', e($settings['site_title']))</title>
    <meta name="description" content="@yield('description', e($settings['site_description']))">
    <meta name="theme-color" content="#151b24">
    <meta property="og:site_name" content="{{ $settings['site_title'] }}">
    <meta property="og:title" content="@yield('title', e($settings['site_title']))">
    <meta property="og:description" content="@yield('description', e($settings['site_description']))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og-type', 'website')">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" type="application/rss+xml" title="{{ $settings['site_title'] }}" href="{{ $blogUrl }}/rss/">
    <link rel="icon" type="image/png" href="/s/img/finnobe3logo.png">
    <link rel="stylesheet" href="/s/blog/blog.css">
    @yield('meta')
</head>
<body class="blog-site @yield('body-class')" style="--blog-accent:{{ $settings['accent_color'] }}">
<div class="site-wrapper">
    <header class="site-header @yield('header-class') {{ $settings['cover_url'] ? 'has-cover' : '' }}">
        @if($settings['cover_url'])
            <img class="site-header-cover-image" src="{{ $settings['cover_url'] }}" alt="" fetchpriority="high">
        @endif
        <div class="site-header-inner">
            <nav class="site-nav" aria-label="Blog navigation">
                <div class="site-nav-left">
                    <a class="site-nav-home" href="/" aria-label="{{ $settings['site_title'] }} home">
                        @if($settings['logo_url'])
                            <img src="{{ $settings['logo_url'] }}" class="site-nav-logo" alt="{{ $settings['site_title'] }}">
                        @else
                            <span>{{ $settings['site_title'] }}</span>
                        @endif
                    </a>
                    <a href="/" class="site-nav-link {{ request()->is('/') ? 'site-nav-current' : '' }}">Home</a>
                    <a href="{{ config('app.url') }}" class="site-nav-link">Finobe</a>
                </div>
                <div class="site-nav-right">
                    @if($settings['social_url'])
                        <a href="{{ $settings['social_url'] }}" class="site-nav-link" rel="noopener noreferrer" target="_blank">Community</a>
                    @endif
                    <a href="/rss/" class="site-nav-icon-link" title="RSS feed" aria-label="RSS feed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="19" r="2"/><path d="M3 11v3a7 7 0 0 1 7 7h3C13 15.477 8.523 11 3 11zm0-8v3c8.284 0 15 6.716 15 15h3C21 11.059 12.941 3 3 3z"/></svg>
                    </a>
                </div>
            </nav>
            @yield('hero')
        </div>
    </header>

    <main id="content" class="site-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer-content">
            <section class="copyright"><a href="/">{{ $settings['site_title'] }}</a> &copy; {{ date('Y') }}</section>
            <nav class="site-footer-nav" aria-label="Footer navigation">
                <a href="/">Latest posts</a>
                <a href="{{ config('app.url') }}">Back to Finobe</a>
                <a href="/rss/">RSS</a>
            </nav>
        </div>
    </footer>
</div>
</body>
</html>
