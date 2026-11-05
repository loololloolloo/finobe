@extends('admin.blog.layout')
@section('title',$post ? 'Edit post' : 'New post')
@section('heading',$post ? 'Edit blog post' : 'New blog post')
@section('content')
<form method="POST" action="{{ $post ? '/admin/blog/posts/'.$post->id : '/admin/blog/posts' }}" class="card mb-3">
@csrf
@if($post)@method('PUT')@endif
<div class="card-header font-weight-bold">Post details</div>
<div class="card-body">
<div class="form-group"><label for="title">Title</label><input id="title" class="form-control" name="title" maxlength="255" required value="{{ old('title',$post->title ?? '') }}"></div>
<div class="row">
<div class="col-md-6 form-group"><label for="slug">Slug <small class="text-muted">(optional; generated automatically)</small></label><input class="form-control" id="slug" name="slug" value="{{ old('slug',$post->slug ?? '') }}"></div>
<div class="col-md-6 form-group"><label for="author_id">Author</label><select class="form-control" id="author_id" name="author_id"><option value="">Finobe Team</option>@foreach($authors as $author)<option value="{{ $author->id }}" {{ (string)old('author_id',$post->author_id ?? '')===(string)$author->id ? 'selected' : '' }}>{{ $author->name }}</option>@endforeach</select></div>
</div>
<div class="form-group"><label for="excerpt">Excerpt</label><textarea class="form-control" id="excerpt" name="excerpt" rows="2">{{ old('excerpt',$post->excerpt ?? '') }}</textarea></div>
<div class="form-group"><label for="body">Post content <small class="text-muted">(Markdown supported)</small></label><textarea class="form-control blog-markdown-input" id="body" name="body" rows="16" required>{{ old('body',$post->body ?? '') }}</textarea></div>
<div class="form-group"><label for="cover_url">Cover image URL</label><input type="url" class="form-control" id="cover_url" name="cover_url" value="{{ old('cover_url',$post->cover_url ?? '') }}"></div>
<div class="row"><div class="col-md-6 form-group"><label for="status">Publish status</label><select id="status" name="status" class="form-control"><option value="draft" {{ old('status',$post->status ?? 'draft')==='draft' ? 'selected' : '' }}>Draft</option><option value="published" {{ old('status',$post->status ?? '')==='published' ? 'selected' : '' }}>Published</option></select></div>
<div class="col-md-6 form-group"><label for="tags">Tags</label><select id="tags" class="form-control blog-tag-select" name="tags[]" multiple size="5">@foreach($tags as $tag)<option value="{{ $tag->id }}" {{ in_array($tag->id,array_map('intval',old('tags',$selectedTags)),true) ? 'selected' : '' }}>{{ $tag->name }}</option>@endforeach</select><small class="text-muted">Use Ctrl/Cmd to select multiple tags.</small></div></div>
<div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:10px"><div class="d-flex align-items-center flex-wrap" style="gap:10px">
<a href="/admin/blog" class="btn btn-outline-secondary">Back to posts</a>
@if($post && $post->status==='published' && $post->published_at && \Carbon\Carbon::parse($post->published_at)->isPast())
<a class="btn btn-outline-primary" href="{{ rtrim(config('blog.url'), '/').'/'.$post->slug.'/' }}" target="_blank" rel="noopener noreferrer"><i class="far fa-eye mr-1"></i>View published post</a>
@endif
</div><button class="btn btn-theme" type="submit">{{ $post ? 'Save changes' : 'Create post' }}</button></div>
</div>
</form>
@if($post)<form method="POST" action="/admin/blog/posts/{{ $post->id }}" onsubmit="return confirm('Permanently delete this post?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete post</button></form>@endif
@endsection
