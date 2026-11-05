@extends('admin.blog.layout')
@section('title','Authors')
@section('heading','Blog authors')
@section('content')
<div class="row">
<div class="col-md-5 mb-4">
<form class="card" method="POST" action="/admin/blog/authors">@csrf
<div class="card-header font-weight-bold">Create author</div><div class="card-body">
<div class="form-group"><label>Name</label><input class="form-control" name="name" required maxlength="100"></div>
<div class="form-group"><label>Bio</label><textarea class="form-control" name="bio" rows="3"></textarea></div>
<div class="form-group"><label>Avatar URL</label><input type="url" class="form-control" name="avatar_url"></div>
<button class="btn btn-theme" type="submit">Add author</button>
</div></form></div>
<div class="col-md-7">
@forelse($authors as $author)
<div class="card mb-3"><div class="card-header"><strong>{{ $author->name }}</strong><span class="text-muted small ml-2">/author/{{ $author->slug }}</span></div>
<div class="card-body"><form method="POST" action="/admin/blog/authors">@csrf
<input type="hidden" name="id" value="{{ $author->id }}">
<div class="form-group"><label>Name</label><input class="form-control" name="name" required value="{{ $author->name }}"></div>
<div class="form-group"><label>Bio</label><textarea name="bio" class="form-control" rows="2">{{ $author->bio }}</textarea></div>
<div class="form-group"><label>Avatar URL</label><input type="url" class="form-control" name="avatar_url" value="{{ $author->avatar_url }}"></div>
<button class="btn btn-sm btn-theme">Save author</button></form>
<form class="mt-2" method="POST" action="/admin/blog/authors/{{ $author->id }}" onsubmit="return confirm('Remove this author? Posts will be kept.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></div></div>
@empty<p class="text-muted">No authors yet.</p>@endforelse
</div></div>
@endsection
