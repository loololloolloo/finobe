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
<script src="/s/admin/workbench.js" defer></script>
</head>
<body class="finobe-admin">
{!! $navbarHtml !!}
<div class="container mt-4 admin-classic">
<main id="main">
<h3 id="page-title" class="font-weight-bold">Admin Panel</h3>
<section id="admin-menu" class="catalog_container mt-4" aria-label="Admin sections">
@php
$icons = [
    'Moderation' => ['gavel', 'danger'], 'Access restrictions' => ['ban', 'danger'],
    'Accounts' => ['user', 'primary'], 'Economy' => ['coins', 'warning'],
    'Catalog' => ['tshirt', 'success'], 'Places' => ['gamepad', 'primary'],
    'Avatar' => ['sync', 'success'], 'Forum' => ['comments', 'warning'],
    'Messages' => ['envelope', 'primary'], 'Invites' => ['ticket-alt', 'success'],
    'Videos' => ['video', 'danger'], 'Announcements' => ['bullhorn', 'warning'],
    'Site settings' => ['cog', 'primary'], 'Staff' => ['user-shield', 'danger'],
    'Audit' => ['history', 'primary'], 'Operations' => ['server', 'success'],
];
@endphp
<div class="row mx-0">
@if(array_intersect(\App\Admin\Access::scopes($actor), ['Accounts','Moderation','Access restrictions','Economy','Avatar','Messages','Staff','Audit']))
<div class="col-6 col-md-4 col-lg-2 mb-4"><a class="catalog-card admin-category" href="/admin/users"><div class="card"><div class="card-body text-center text-primary"><i class="fas align-middle fa-fw fa-users mr-1 fa-4x" aria-hidden="true"></i><br><br><p class="mb-0">Manage users</p></div></div></a></div>
@endif
@foreach(collect($actions)->groupBy('group') as $group => $items)
@php([$icon, $color] = $icons[$group] ?? ['tools', 'primary'])
<div class="col-6 col-md-4 col-lg-2 mb-4">
<a class="catalog-card admin-category" href="#{{ \Illuminate\Support\Str::slug($group) }}" data-group="{{ $group }}">
<div class="card"><div class="card-body text-center text-{{ $color }}">
<i class="fas align-middle fa-fw fa-{{ $icon }} mr-1 fa-4x" aria-hidden="true"></i><br><br>
<p class="mb-0">{{ $group }}</p>
</div></div></a></div>
@endforeach
@if(in_array('Blog', \App\Admin\Access::scopes($actor), true))
<div class="col-6 col-md-4 col-lg-2 mb-4"><a class="catalog-card admin-category" href="/admin/blog"><div class="card"><div class="card-body text-center text-primary"><i class="fas align-middle fa-fw fa-newspaper mr-1 fa-4x" aria-hidden="true"></i><br><br><p class="mb-0">Blog</p></div></div></a></div>
@endif
<div class="col-6 col-md-4 col-lg-2 mb-4"><a class="catalog-card admin-category" href="/admin/assets"><div class="card"><div class="card-body text-center text-success"><i class="fas align-middle fa-fw fa-check-circle mr-1 fa-4x" aria-hidden="true"></i><br><br><p class="mb-0">Asset review</p></div></div></a></div>
</div></section>
<div id="admin-workspace" hidden>
<p><a href="#" id="admin-back">← Admin Panel</a></p>
<section class="lookup card card-body"><label for="user-query">Find an account</label><form id="lookup-form"><input class="form-control" id="user-query" name="q" placeholder="Username or user ID" autocomplete="off" maxlength="80" required><button class="btn btn-primary" type="submit">Look up</button></form><span id="lookup-status" role="status">Open an account to see its moderation history.</span></section>
<section id="tools" class="card"><div class="tools-bar"><label for="tool-search">Find a control</label><input class="form-control" type="search" id="tool-search" placeholder="Search controls, sections, or keywords…"><label class="check"><input type="checkbox" id="favorites-only"> Starred only</label><span id="tool-count"></span></div><div class="table-wrap"><table class="tools-table table table-sm table-striped mb-0"><thead><tr><th class="star-col"><span class="sr-only">Favorite</span>☆</th><th>Control</th><th>Section</th><th>Target</th><th>Type</th><th></th></tr></thead><tbody id="tool-list"></tbody></table></div></section>
<section id="result-panel" class="panel card mt-4" hidden><div class="panel-title card-header"><h2 id="result-title">Result</h2><button id="download-result" class="link-button">Download JSON</button></div><div id="result" aria-live="polite"></div><div class="result-pagination" hidden><button id="prev-page">← Previous</button><span id="result-page"></span><button id="next-page">Next →</button></div></section>
</div>
<div id="notice" role="status" aria-live="polite" hidden></div>
<footer class="admin-footer"><span>Finobe Administration</span><span><a href="/legal/rules">Rules</a> | <a href="/legal/terms">Terms of Service</a></span></footer>
</main></div>
<dialog id="action-dialog" aria-labelledby="action-title"><form id="action-form"><header><span id="action-section"></span><button type="button" id="close-dialog" aria-label="Close">×</button></header><h2 id="action-title"></h2><p id="action-description"></p><label id="target-label" for="action-target">Target</label><input class="form-control" id="action-target" name="target" autocomplete="off" maxlength="80"><div id="value-control"></div><div id="reason-control"><label for="action-reason">Reason <small>Required for the audit log</small></label><textarea class="form-control" id="action-reason" name="reason" rows="3" minlength="3" maxlength="2000"></textarea></div><label id="confirm-control" class="confirm"><input id="action-confirm" type="checkbox"> I checked the target and understand this action.</label><div id="action-error" role="alert" hidden></div><footer><button type="button" id="cancel-dialog">Cancel</button><button id="execute-action" class="primary btn btn-primary" type="submit">Apply action</button></footer></form></dialog>
<script id="admin-data" type="application/json">{!! json_encode(['actions'=>$actions,'actorId'=>$actor->id], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
</body></html>
