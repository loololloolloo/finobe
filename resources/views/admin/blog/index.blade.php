@extends('admin.blog.layout')
@section('title','Posts')
@section('heading','Blog posts')
@section('content')
<div class="card">
<div class="card-header d-flex justify-content-between align-items-center"><strong>Published and draft posts</strong><a href="/admin/blog/posts/new" class="btn btn-theme btn-sm"><i class="fas fa-plus mr-1"></i>New post</a></div>
<div class="table-responsive">
<table class="table table-striped table-sm mb-0">
<thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Published</th><th></th></tr></thead>
<tbody>
@forelse($posts as $post)
<tr>
<td><a href="/admin/blog/posts/{{ $post->id }}/edit">{{ $post->title }}</a><div class="small text-muted">/{{ $post->slug }}</div></td>
<td>{{ $post->author_name ?: 'Finobe Team' }}</td>
<td><span class="badge {{ $post->status==='published' ? 'badge-success' : 'badge-secondary' }}">{{ ucfirst($post->status) }}</span></td>
<td>{{ $post->published_at ?: '—' }}</td>
<td><a href="/admin/blog/posts/{{ $post->id }}/edit" class="btn btn-sm btn-outline-secondary">Edit</a></td>
</tr>
@empty<tr><td colspan="5" class="text-muted py-4 text-center">No blog posts yet. Create the first one above.</td></tr>@endforelse
</tbody>
</table></div>
</div>
<div class="mt-3">{{ $posts->links() }}</div>
@endsection
