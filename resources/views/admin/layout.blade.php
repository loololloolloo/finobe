<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Administration') - Finobe</title>
<link rel="icon" href="/s/img/finnobe3logo.png">
<link rel="stylesheet" href="/s/css/{{ in_array((string)($actor->theme ?? '0'),['1','night'],true) ? 'night' : 'light' }}.css">
<link rel="stylesheet" href="/s/css/all.min.css">
<link rel="stylesheet" href="/s/admin/finobe-admin.css">
</head><body>
{!! $navbarHtml !!}
<main class="container mt-4 mb-5">
@if(session('admin_success'))<div role="status" class="alert alert-success">{{ session('admin_success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
</body></html>
