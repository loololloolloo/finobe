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
<div class="container mt-4 admin-shell">
<aside class="sidebar"><div class="side-heading">Administration</div><nav aria-label="Admin sections">
@foreach(collect($actions)->groupBy('group') as $group => $items)<button class="section-link nav-link" data-group="{{ $group }}"><span>{{ $group }}</span></button>@endforeach
</nav><div class="side-bottom"><p class="mt-2"><a href="/admin/assets">Asset review</a></p></div></aside>
<main id="main"><div class="breadcrumbs"><a href="/admin">Administration</a> <span>›</span> <span id="crumb">Overview</span></div>
<div class="page-heading"><h1 id="page-title">Admin Panel</h1></div>
<section class="lookup card card-body"><label for="user-query">Find an account</label><form id="lookup-form"><input class="form-control" id="user-query" name="q" placeholder="Username or user ID" autocomplete="off" maxlength="80" required><button class="btn btn-primary" type="submit">Look up</button></form><span id="lookup-status" role="status">Open an account to see its moderation history.</span></section>
<section id="account" class="account-panel card" hidden><div class="account-heading"><div><h2 id="account-name"></h2><p id="account-meta"></p></div><a id="profile-link" href="#" target="_blank" rel="noopener">View public profile ↗</a></div><div class="account-tabs nav nav-tabs"><button class="nav-link active" data-account-tab="actions">Actions</button><button class="nav-link" data-account-tab="history">Moderation history</button></div><div id="account-actions"><form id="moderation-form"><fieldset><legend>Account state override</legend><div class="radio-options"><label><input type="radio" name="moderation" value="moderation-send-a-reminder"> Remind</label><label><input type="radio" name="moderation" value="moderation-issue-a-warning" checked> Warn</label>@foreach([1,3,7,14,30] as $days)<label><input type="radio" name="moderation" value="moderation-ban-for-{{ $days }}-day{{ $days == 1 ? '' : 's' }}"> Ban {{ $days }} day{{ $days == 1 ? '' : 's' }}</label>@endforeach<label class="red"><input type="radio" name="moderation" value="moderation-terminate-account"> Terminate</label></div></fieldset><div class="moderation-note"><label for="moderation-reason">Moderation note <small>Shown to the user</small></label><textarea class="form-control" id="moderation-reason" rows="2" minlength="3" maxlength="2000" placeholder="Explain what happened and what needs to change." required></textarea><button class="primary btn btn-primary" type="submit">Review action</button></div></form></div><div id="account-history" hidden></div></section>
<section id="tools" class="card"><div class="tools-bar"><label for="tool-search">Find a control</label><input class="form-control" type="search" id="tool-search" placeholder="Search controls, sections, or keywords…"><label class="check"><input type="checkbox" id="favorites-only"> Starred only</label><span id="tool-count"></span></div><div class="table-wrap"><table class="tools-table table table-sm table-striped mb-0"><thead><tr><th class="star-col"><span class="sr-only">Favorite</span>☆</th><th>Control</th><th>Section</th><th>Target</th><th>Type</th><th></th></tr></thead><tbody id="tool-list"></tbody></table></div></section>
<section id="result-panel" class="panel card mt-4" hidden><div class="panel-title card-header"><h2 id="result-title">Result</h2><button id="download-result" class="link-button">Download JSON</button></div><div id="result" aria-live="polite"></div><div class="result-pagination" hidden><button id="prev-page">← Previous</button><span id="result-page"></span><button id="next-page">Next →</button></div></section>
<div id="notice" role="status" aria-live="polite" hidden></div>
<footer class="admin-footer"><span>Finobe Administration</span><span><a href="/legal/rules">Rules</a> | <a href="/legal/terms">Terms of Service</a></span></footer>
</main></div>
<dialog id="action-dialog" aria-labelledby="action-title"><form id="action-form"><header><span id="action-section"></span><button type="button" id="close-dialog" aria-label="Close">×</button></header><h2 id="action-title"></h2><p id="action-description"></p><label id="target-label" for="action-target">Target</label><input class="form-control" id="action-target" name="target" autocomplete="off" maxlength="80"><div id="value-control"></div><div id="reason-control"><label for="action-reason">Reason <small>Required for the audit log</small></label><textarea class="form-control" id="action-reason" name="reason" rows="3" minlength="3" maxlength="2000"></textarea></div><label id="confirm-control" class="confirm"><input id="action-confirm" type="checkbox"> I checked the target and understand this action.</label><div id="action-error" role="alert" hidden></div><footer><button type="button" id="cancel-dialog">Cancel</button><button id="execute-action" class="primary btn btn-primary" type="submit">Apply action</button></footer></form></dialog>
<script id="admin-data" type="application/json">{!! json_encode(['actions'=>$actions,'actorId'=>$actor->id], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
</body></html>
