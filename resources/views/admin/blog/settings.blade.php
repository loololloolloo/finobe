@extends('admin.blog.layout')
@section('title','Settings')
@section('heading','Blog settings')
@section('content')
<form action="/admin/blog/settings" method="POST" class="card">@csrf<div class="card-header font-weight-bold">Public blog appearance</div>
<div class="card-body">
@foreach(['site_title'=>'Site title','site_description'=>'Description','logo_url'=>'Logo URL','cover_url'=>'Header cover URL','social_url'=>'Community link'] as $key=>$label)
<div class="form-group"><label for="{{ $key }}">{{ $label }}</label>
@if($key==='site_description')<textarea id="{{ $key }}" name="{{ $key }}" class="form-control" rows="2">{{ old($key,$blogSettings[$key]) }}</textarea>
@else<input id="{{ $key }}" name="{{ $key }}" class="form-control" {{ in_array($key,['logo_url','cover_url','social_url']) ? 'type=url' : 'type=text' }} value="{{ old($key,$blogSettings[$key]) }}" {{ $key==='site_title' ? 'required' : '' }}>@endif
</div>
@endforeach
<div class="form-group"><label for="accent_color">Accent color</label><input type="color" id="accent_color" name="accent_color" class="form-control" style="max-width:100px" value="{{ old('accent_color',$blogSettings['accent_color']) }}"></div>
<button type="submit" class="btn btn-theme">Save settings</button>
</div></form>
@endsection
