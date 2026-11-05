<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Blog manager') - Finobe</title>
<link rel="icon" href="/s/img/finnobe3logo.png">
<link rel="stylesheet" href="/s/css/{{ in_array((string)($actor->theme ?? '0'),['1','night'],true) ? 'night' : 'light' }}.css">
<link rel="stylesheet" href="/s/css/all.min.css">
<link rel="stylesheet" href="/s/admin/blog-admin.css">
</head><body>
{!! $navbarHtml !!}
<main class="container mt-4 mb-5">
<header class="d-flex flex-wrap justify-content-between align-items-center mb-3">
<h3 class="font-weight-bold mb-2">@yield('heading','Blog management')</h3>
<a href="{{ config('blog.url') }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">View blog</a>
</header>
<nav class="blog-admin-nav mb-4" aria-label="Blog administration">
<a class="{{ request()->is('admin/blog') ? 'active' : '' }}" href="/admin/blog"><i class="fas fa-file-alt fa-fw"></i> Posts</a>
<a class="{{ request()->is('admin/blog/authors') ? 'active' : '' }}" href="/admin/blog/authors"><i class="fas fa-users fa-fw"></i> Authors</a>
<a class="{{ request()->is('admin/blog/tags') ? 'active' : '' }}" href="/admin/blog/tags"><i class="fas fa-tags fa-fw"></i> Tags</a>
<a class="{{ request()->is('admin/blog/settings') ? 'active' : '' }}" href="/admin/blog/settings"><i class="fas fa-cog fa-fw"></i> Settings</a>
</nav>
@if(session('admin_success'))<div class="alert alert-success">{{ session('admin_success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
</body></html>
