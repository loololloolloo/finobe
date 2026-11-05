@extends('admin.layout')
@section('title','Manage users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h3 class="font-weight-bold mb-0">Manage users</h3><a href="/admin" class="btn btn-sm btn-outline-secondary">Admin panel</a></div>
<div class="card mb-4"><div class="card-header font-weight-bold"><i class="fas fa-search mr-2" aria-hidden="true"></i>Find a user</div>
<div class="card-body"><form method="GET" action="/admin/users" class="d-flex flex-wrap align-items-center" style="gap:10px">
<input name="q" class="form-control flex-grow-1" style="max-width:440px" value="{{ $q }}" placeholder="Username, email, or user ID" aria-label="Find a user">
<button class="btn btn-theme" type="submit">Search</button></form></div></div>
<div class="card"><div class="card-header font-weight-bold">User accounts</div><div class="table-responsive">
<table class="table table-sm table-striped mb-0"><thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Dius</th><th>Joined</th><th></th></tr></thead><tbody>
@forelse($users as $user)<tr><td>{{ $user->id }}</td><td><a href="/admin/users/{{ $user->id }}">{{ $user->username }}</a></td><td>{{ ucfirst($user->status) }}</td><td class="text-nowrap"><img src="/s/img/diu_16.png" alt="" width="16" height="16"> {{ number_format($user->Dius) }}</td><td>{{ $user->created ?: 'Unknown' }}</td><td><a class="btn btn-sm btn-outline-secondary" href="/admin/users/{{ $user->id }}">Manage</a></td></tr>
@empty<tr><td colspan="6" class="text-muted text-center py-4">No matching users.</td></tr>@endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
