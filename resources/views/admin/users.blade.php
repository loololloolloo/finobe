<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Administration - Finobe</title>
<link rel="icon" href="/s/img/finnobe3logo.png">
<link rel="stylesheet" href="/s/css/{{ in_array($actor->theme, ['1', 1, 'night'], true) ? 'night' : 'light' }}.css">
<link rel="stylesheet" href="/s/admin/workbench.css">
<link rel="stylesheet" href="/s/css/all.min.css">
<script src="/s/admin/jquery.min.js"></script>
<script src="/s/admin/bootstrap.bundle.min.js"></script>

</head>
<body class="finobe-admin">
{!! $navbarHtml !!}
<main class="container mt-4">
<p><a href="/admin">← Admin Panel</a></p>
<h3 class="font-weight-bold">Manage users</h3>
<form method="get" action="/admin/users" class="form-inline my-4">
<label for="user-search" class="mr-2">Username or user ID</label>
<input id="user-search" name="q" class="form-control mr-2" value="{{ $query }}" maxlength="80">
<button class="btn btn-primary" type="submit">Search</button>
</form>
<div class="card table-responsive"><table class="table table-striped mb-0"><thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Joined</th><th>Last login</th><th></th></tr></thead><tbody>
@forelse($users as $user)
<tr><td>{{ $user->id }}</td><td><a href="/admin/users/{{ $user->id }}">{{ $user->username }}</a></td><td>{{ $user->status }}</td><td>{{ $user->created }}</td><td>{{ $user->lastlogin }}</td><td><a href="/admin/users/{{ $user->id }}">Manage</a></td></tr>
@empty<tr><td colspan="6">No users found.</td></tr>@endforelse
</tbody></table></div>
<nav aria-label="User pages" class="my-4 d-flex justify-content-between">
@if($users->previousPageUrl())<a href="{{ $users->previousPageUrl() }}">← Previous</a>@else<span></span>@endif
<span>Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
@if($users->nextPageUrl())<a href="{{ $users->nextPageUrl() }}">Next →</a>@else<span></span>@endif
</nav></main></body></html>
