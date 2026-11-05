<!doctype html>
<html lang="en" class="dark-mode no-infinite-scroll">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', e($settings['site_title']))</title>
    <meta name="description" content="@yield('description', e($settings['site_description']))">
    <meta property="og:site_name" content="{{ $settings['site_title'] }}">
    <meta property="og:type" content="@yield('og-type', 'website')">
    <meta property="og:title" content="@yield('title', e($settings['site_title']))">
    <meta property="og:description" content="@yield('description', e($settings['site_description']))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="image/png" href="/s/img/finnobe3logo.png">
    <link rel="alternate" type="application/rss+xml" title="{{ $settings['site_title'] }}" href="{{ rtrim($blogUrl, '/') }}/rss/">
    <link rel="stylesheet" href="/s/blog/casper-screen.css">
    <link rel="stylesheet" href="/s/blog/blog.css">
    @yield('meta')
    <script defer src="/s/blog/finobe-blog.js"></script>
</head>
<body class="@yield('body-class', 'home-template') is-head-left-logo has-serif-title is-dropdown-loaded @yield('cover-class')">
<div class="viewport">
    <header id="gh-head" class="gh-head outer">
        <div class="gh-head-inner inner">
            <div class="gh-head-brand">
                <a class="gh-head-logo" href="/">
                    <img src="{{ $settings['logo_url'] ?: '/s/img/finnobe3logo.png' }}" alt="{{ $settings['site_title'] }}">
                </a>
                <button type="button" class="gh-search gh-icon-btn" aria-label="Search this site" data-blog-search>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button type="button" class="gh-burger" aria-label="Main Menu" aria-expanded="false" data-blog-menu></button>
            </div>
            <nav class="gh-head-menu" aria-label="Main navigation">
                <ul class="nav">
                    <li class="nav-home {{ request()->is('/') ? 'nav-current' : '' }}"><a href="/">HOME</a></li>
                    <li class="nav-main-website"><a href="{{ config('app.url') }}">MAIN WEBSITE</a></li>
                </ul>
            </nav>
            <div class="gh-head-actions">
                <button type="button" class="gh-search gh-icon-btn" aria-label="Search this site" data-blog-search>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </div>
    </header>
    <div class="site-content">
        @yield('hero')
        @yield('content')
    </div>
    <footer class="site-footer outer">
        <div class="inner">
            <section class="copyright"><a href="/">{{ $settings['site_title'] }}</a> &copy; {{ date('Y') }}</section>
            <nav class="site-footer-nav" aria-label="Footer navigation"></nav>
            <div class="gh-powered-by"><a href="https://ghost.org/themes/casper/" target="_blank" rel="noopener noreferrer">Casper theme</a></div>
        </div>
    </footer>
</div>
<div class="blog-search-overlay" id="blog-search" hidden role="dialog" aria-modal="true" aria-labelledby="blog-search-title">
    <div class="blog-search-window">
        <div class="blog-search-bar">
            <h2 id="blog-search-title">Search this site</h2>
            <button type="button" class="blog-search-close" aria-label="Close search" data-blog-search-close>&times;</button>
        </div>
        <form action="/search" method="GET" role="search">
            <label class="blog-search-label" for="blog-search-query">Search articles</label>
            <input id="blog-search-query" type="search" name="q" minlength="2" maxlength="120" required placeholder="Search posts...">
            <button type="submit">Search</button>
        </form>
    </div>
</div>
</body>
</html>
