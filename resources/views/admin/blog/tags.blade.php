@extends('admin.blog.layout')
@section('title','Tags')
@section('heading','Blog tags')
@section('content')
<div class="row"><div class="col-md-5 mb-4"><form class="card" action="/admin/blog/tags" method="POST">@csrf
<div class="card-header font-weight-bold">New tag</div><div class="card-body">
<div class="form-group"><label>Name</label><input class="form-control" name="name" required maxlength="100"></div>
<div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="2"></textarea></div>
<button class="btn btn-theme">Add tag</button></div></form></div>
<div class="col-md-7">
@forelse($tags as $tag)<div class="card mb-3"><div class="card-header"><strong>{{ $tag->name }}</strong><span class="text-muted small ml-2">/tag/{{ $tag->slug }}</span></div>
<div class="card-body"><form action="/admin/blog/tags" method="POST">@csrf
<input type="hidden" name="id" value="{{ $tag->id }}">
<div class="form-group"><label>Name</label><input class="form-control" name="name" value="{{ $tag->name }}" required></div>
<div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="2">{{ $tag->description }}</textarea></div>
<button class="btn btn-sm btn-theme">Save tag</button></form>
<form method="POST" action="/admin/blog/tags/{{ $tag->id }}" class="mt-2" onsubmit="return confirm('Remove this tag from all posts?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form>
</div></div>@empty<p class="text-muted">No tags yet.</p>@endforelse
</div></div>
@endsection
