@extends('admin.layout')
@section('title','Manage '.$subject->username)
@section('content')
<p class="mb-2"><a href="/admin/users"><i class="fas fa-chevron-left fa-fw mr-1" aria-hidden="true"></i>Manage users</a></p>
<h3 class="font-weight-bold mb-3">Manage user</h3>
<div class="row">
<div class="col-lg-9 admin-user-main">
<div class="card admin-account-summary mb-4"><div class="card-body">
<div class="admin-summary-top">
    <img class="admin-summary-avatar" src="{{ $subject->pfp ? ('https://cdn.finobe.wtf/'.ltrim($subject->pfp,'/')) : '/s/img/finnobe3logo.png' }}" alt="{{ $subject->username }}'s avatar">
    <div class="admin-summary-person">
      <h4 class="admin-summary-name">{{ $subject->username }}</h4>
      <div class="admin-summary-identity"><span class="badge badge-{{ $activeBan ? 'danger' : 'success' }}">{{ $activeBan ? 'Banned' : 'Active' }}</span><span>{{ ucfirst($subject->status) }}</span><span>User #{{ $subject->id }}</span></div>
      <a class="admin-summary-public-link" href="/user/{{ $subject->id }}">View public profile</a>
    </div>
    <div class="admin-summary-balance"><span>Dius balance</span><div class="admin-summary-amount"><img src="/s/img/diu_32.png" alt="" width="32" height="32">{{ number_format($subject->Dius) }}</div></div>
</div>
<div class="admin-summary-facts"><div><span>Joined</span><strong>{{ $subject->created ?: 'Unknown' }}</strong></div><div><span>Last login</span><strong>{{ $subject->lastlogin ?: 'Never' }}</strong></div></div>
</div></div>
<h4 class="mb-3">{{ $sectionTitle }}</h4>
@if($section==='overview')
<div class="card mb-3"><div class="card-header font-weight-bold">Quick actions</div><div class="card-body"><div class="admin-overview-actions">
<a href="/admin/users/{{ $subject->id }}/moderation"><i class="fas fa-gavel"></i><span><strong>Warn or ban</strong><small>Review moderation actions</small></span></a>
<a href="/admin/users/{{ $subject->id }}/account"><i class="fas fa-user-cog"></i><span><strong>Edit account</strong><small>Update account information</small></span></a>
<a href="/admin/users/{{ $subject->id }}/notes"><i class="fas fa-sticky-note"></i><span><strong>Staff notes</strong><small>Private moderation notes</small></span></a>
<a href="/admin/users/{{ $subject->id }}/economy"><i class="fas fa-exchange-alt"></i><span><strong>Transactions</strong><small>Manage and review Dius</small></span></a>
</div></div></div>
@if($activeBan)<div class="card mb-3"><div class="card-header">Active ban</div><div class="card-body">{{ $activeBan->reason }} <a href="/admin/users/{{ $subject->id }}/moderation">Review</a></div></div>@endif
@if(trim((string)($subject->blurb ?? ''))!=='')<div class="card"><div class="card-header">Profile blurb</div><div class="card-body">{{ $subject->blurb }}</div></div>@endif
@elseif($section==='account')
<div class="card"><div class="card-header font-weight-bold">Account details</div><div class="card-body">
<form method="POST" action="/admin/users/{{ $subject->id }}/account">@csrf
<div class="form-group"><label>Username</label><input class="form-control" readonly value="{{ $subject->username }}"><small class="text-muted">Changing usernames requires a separate audited process.</small></div>
<div class="form-group"><label>Email address</label><input class="form-control" type="email" name="email" value="{{ old('email',$subject->email) }}" required maxlength="255"></div>
<div class="form-group"><label>Account role</label><input class="form-control" value="{{ ucfirst($subject->status) }}" readonly></div>
<button class="btn btn-theme">Save email</button></form>
</div></div>
@elseif($section==='moderation')
<div class="card mb-3"><div class="card-header font-weight-bold">Moderation action</div><div class="card-body">
<form action="/admin/users/{{ $subject->id }}/moderation" method="POST">@csrf
<div class="row"><div class="col-md-6 form-group"><label>Action</label><select name="action" class="form-control"><option value="warn">Warn user</option><option value="ban">Ban user</option>@if($activeBan)<option value="unban">Lift active ban</option>@endif</select></div><div class="col-md-6 form-group"><label>Temporary ban length (days)</label><input type="number" name="days" class="form-control" value="7" min="1" max="3650"></div></div>
<div class="form-group"><label>Reason</label><textarea class="form-control" name="reason" rows="3" maxlength="1000"></textarea></div>
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="permanent" value="1" id="permanent"><label class="form-check-label" for="permanent">Permanent ban (only when banning)</label></div>
<button class="btn btn-theme">Apply moderation action</button>
</form></div></div>
<div class="card mb-3"><div class="card-header">Ban history</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Moderator</th><th>Reason</th><th>Expires</th></tr></thead><tbody>
@forelse($bans as $ban)<tr><td>{{ $ban->date }}</td><td>{{ $ban->moderator }}</td><td>{{ $ban->reason }}</td><td>{{ $ban->perm ? 'Permanent' : $ban->expire }} @if($ban->undone)(Overturned)@endif</td></tr>@empty<tr><td colspan="4">No bans.</td></tr>@endforelse
</tbody></table></div></div>
<div class="card"><div class="card-header">Warning history</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Moderator</th><th>Reason</th></tr></thead><tbody>
@forelse($warnings as $warning)<tr><td>{{ $warning->date }}</td><td>{{ $warning->moderator }}</td><td>{{ $warning->reason }}</td></tr>@empty<tr><td colspan="3">No warnings.</td></tr>@endforelse
</tbody></table></div></div>
@elseif($section==='notes')
<div class="card mb-3"><div class="card-header">Add staff note</div><div class="card-body"><form method="POST" action="/admin/users/{{ $subject->id }}/notes">@csrf
<textarea name="body" class="form-control mb-2" rows="4" maxlength="5000" required placeholder="Visible to administrators only"></textarea><button class="btn btn-theme">Add private note</button></form></div></div>
@forelse($notes as $note)<div class="card mb-2"><div class="card-header">{{ $note->created_at }} · Staff #{{ $note->moderator_id }}</div><div class="card-body">{{ $note->body }}</div></div>@empty<p class="text-muted">No staff notes.</p>@endforelse
{{ $notes->links() }}
@elseif($section==='economy')
<div class="card mb-3"><div class="card-header font-weight-bold">Adjust Dius</div><div class="card-body"><form method="POST" action="/admin/users/{{ $subject->id }}/dius">@csrf
<div class="row"><div class="col-md-3 form-group"><label>Amount (+/-)</label><input type="number" class="form-control" name="amount" required></div><div class="col-md-9 form-group"><label>Reason</label><input class="form-control" name="reason" required maxlength="500"></div></div><button class="btn btn-theme">Apply adjustment</button></form></div></div>
<div class="card"><div class="card-header">Recent transactions</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Asset</th></tr></thead><tbody>
@forelse($transactions as $transaction)<tr><td>{{ $transaction->date }}</td><td>{{ $transaction->type }}</td><td><img src="/s/img/diu_16.png" alt="" width="16"> {{ number_format($transaction->amount) }}</td><td>{{ $transaction->assetid }}</td></tr>@empty<tr><td colspan="4">No transactions.</td></tr>@endforelse
</tbody></table></div></div>
@elseif($section==='avatar')
<div class="card"><div class="card-header">Avatar</div><div class="card-body"><p>View and inspect the user's currently rendered avatar.</p><a href="/user/{{ $subject->id }}" class="btn btn-outline-secondary btn-sm">Open profile</a></div></div>
@elseif($section==='messages')
<div class="card"><div class="card-header">Message overview</div><div class="card-body"><strong>{{ number_format($messages) }}</strong> messages sent or received. Message contents are not exposed here.</div></div>
@elseif($section==='changes')
<div class="card"><div class="card-header">Staff activity</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Moderator</th><th>Action</th><th>Details</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td>{{ $log->created_at }}</td><td>#{{ $log->moderator_id }}</td><td>{{ $log->action }}</td><td>{{ $log->details }}</td></tr>@empty<tr><td colspan="4">No staff actions recorded.</td></tr>@endforelse
</tbody></table></div></div>
@endif
</div>
<aside class="col-lg-3 mb-4 admin-user-side">
<div class="card admin-user-menu mb-3"><div class="card-header font-weight-bold"><i class="fas fa-sliders-h fa-fw mr-2" aria-hidden="true"></i>User management</div>
<nav class="admin-user-navigation" aria-label="User management sections">
@foreach(['ACCOUNT'=>['overview','account','avatar','economy'],'MODERATION'=>['moderation','notes','messages'],'ADMINISTRATION'=>['changes']] as $heading=>$keys)
<div class="admin-nav-heading">{{ $heading }}</div>
@foreach($keys as $key)
@if(isset($sections[$key]))
<a class="admin-nav-link {{ $section===$key ? 'is-active' : '' }}" href="/admin/users/{{ $subject->id }}{{ $key==='overview' ? '' : '/'.$key }}" @if($section===$key)aria-current="page"@endif>
<i class="fas fa-fw fa-{{ ['overview'=>'tachometer-alt','account'=>'user-cog','avatar'=>'user-circle','economy'=>'exchange-alt','moderation'=>'gavel','notes'=>'sticky-note','messages'=>'envelope','changes'=>'history'][$key] }}" aria-hidden="true"></i><span>{{ $sections[$key] }}</span></a>
@endif
@endforeach
@endforeach
</nav>
</div>
</aside>
</div>
@endsection
