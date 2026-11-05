@extends('admin.layout')
@section('title','Manage users')
@section('content')
<div class="admin-user-page admin-user-index">
  <div class="admin-breadcrumb"><a href="/admin">Administration</a><span aria-hidden="true">/</span><span>Users</span></div>
  <div class="admin-user-heading">
    <div><h3 class="admin-user-heading-title">Manage users</h3><p class="admin-user-heading-subtitle mb-0">Search and manage user accounts</p></div>
    <a href="/admin" class="btn btn-sm btn-outline-secondary"><i class="fas fa-chevron-left mr-1" aria-hidden="true"></i> Admin panel</a>
  </div>
  <div class="card mb-3">
    <div class="card-header"><strong>Find a user</strong></div>
    <div class="card-body">
      <form method="GET" action="/admin/users" class="admin-user-search">
        <label for="admin-user-search" class="sr-only">Search users</label>
        <input id="admin-user-search" class="form-control" type="search" name="q" value="{{ $q }}" placeholder="Username, email, or User ID" maxlength="150">
        <button type="submit" class="btn btn-theme"><i class="fas fa-search mr-1" aria-hidden="true"></i> Search</button>
        @if($q !== '')<a href="/admin/users" class="btn btn-outline-secondary">Clear</a>@endif
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><strong>{{ $q !== '' ? 'Search results' : 'User accounts' }}</strong></div>
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead><tr><th scope="col">ID</th><th scope="col">Username</th><th scope="col">Role</th><th scope="col">Dius</th><th scope="col">Joined</th><th scope="col"><span class="sr-only">Manage</span></th></tr></thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td class="user-id">#{{ $user->id }}</td>
              <td><a class="user-name-link" href="/admin/users/{{ $user->id }}">{{ $user->username }}</a></td>
              <td>{{ ucfirst($user->status) }}</td>
              <td class="text-nowrap"><img src="/s/img/diu_16.png" width="16" height="16" alt=""> {{ number_format($user->Dius) }}</td>
              <td>{{ $user->created ?: 'Unknown' }}</td>
              <td class="text-right"><a class="btn btn-sm btn-outline-secondary" href="/admin/users/{{ $user->id }}">Manage</a></td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-muted text-center py-4">No matching users.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($users->hasPages())<div class="mt-3">{{ $users->links() }}</div>@endif
</div>
@endsection
