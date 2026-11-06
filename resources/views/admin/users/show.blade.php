@extends('admin.layout')
@section('title','Manage '.$subject->username)
@section('content')
<div class="admin-user-page">
  <div class="admin-breadcrumb"><a href="/admin">Administration</a><span aria-hidden="true">/</span><a href="/admin/users">Users</a><span aria-hidden="true">/</span><span>{{ $subject->username }}</span></div>
  <div class="admin-user-heading">
    <div><h3 class="admin-user-heading-title">Manage {{ $subject->username }}</h3><p class="admin-user-heading-subtitle mb-0">Account and moderation settings</p></div>
    <a class="btn btn-sm btn-outline-secondary" href="/user/{{ $subject->id }}"><i class="far fa-user mr-1" aria-hidden="true"></i> Public profile</a>
  </div>
  <div class="row admin-user-workspace">
    <div class="col-lg-9 admin-user-main">
      <section class="card admin-user-profile mb-3">
        <div class="card-body">
          <div class="admin-user-profile-top">
            <div class="admin-user-avatar-wrap">
              @if($subject->pfp)
                <img class="admin-user-avatar" src="{{ str_starts_with((string)$subject->pfp,'http') ? $subject->pfp : 'https://cdn.finobe.wtf/'.ltrim($subject->pfp,'/') }}" alt="{{ $subject->username }}'s avatar">
              @else
                <div class="admin-user-avatar admin-user-avatar-placeholder" aria-label="No avatar">F</div>
              @endif
            </div>
            <div class="admin-user-identity">
              <h4>{{ $subject->username }}</h4>
              <div class="admin-user-identity-sub">User ID #{{ $subject->id }} <span aria-hidden="true">·</span> {{ ucfirst($subject->status) }}</div>
              <div class="admin-user-status {{ $activeBan ? 'admin-user-status-banned' : 'admin-user-status-active' }}">
                <span class="admin-user-status-dot" aria-hidden="true"></span>{{ $activeBan ? 'Banned' : 'Active' }}
              </div>
            </div>
          </div>
          <div class="admin-user-details">
            <div><span class="admin-detail-label">Joined</span><strong>{{ $subject->created ?: 'Unknown' }}</strong></div>
            <div><span class="admin-detail-label">Last login</span><strong>{{ $subject->lastlogin ?: 'Never' }}</strong></div>
            <div><span class="admin-detail-label">Dius</span><strong class="admin-detail-dius"><img src="/s/img/diu_16.png" width="16" height="16" alt="">{{ number_format($subject->Dius ?? 0) }}</strong></div>
          </div>
        </div>
      </section>
      @if($section==='overview')
        <section class="card admin-user-section mb-3">
          <div class="card-header"><strong>Overview</strong></div>
          <div class="card-body">
            <div class="admin-user-overview-info">
              <div class="admin-user-info-row"><span>Username</span><strong>{{ $subject->username }}</strong></div>
              <div class="admin-user-info-row"><span>Account role</span><strong>{{ ucfirst($subject->status) }}</strong></div>
              <div class="admin-user-info-row"><span>Account status</span><strong>{{ $activeBan ? 'Banned' : 'Active' }}</strong></div>
            </div>
            <div class="admin-user-actions-heading">Manage this user</div>
            <div class="admin-user-actions">
              <a class="btn btn-sm btn-outline-secondary" href="/admin/users/{{ $subject->id }}/account"><i class="fas fa-user-cog fa-fw mr-1" aria-hidden="true"></i>Edit account</a>
              <a class="btn btn-sm btn-outline-secondary" href="/admin/users/{{ $subject->id }}/moderation"><i class="fas fa-gavel fa-fw mr-1" aria-hidden="true"></i>Moderation</a>
              <a class="btn btn-sm btn-outline-secondary" href="/admin/users/{{ $subject->id }}/notes"><i class="far fa-sticky-note fa-fw mr-1" aria-hidden="true"></i>Staff notes</a>
              <a class="btn btn-sm btn-outline-secondary" href="/admin/users/{{ $subject->id }}/economy"><img src="/s/img/diu_16.png" width="14" height="14" alt="" class="mr-1">Dius &amp; transactions</a>
            </div>
          </div>
        </section>
        @if($activeBan)
          <section class="card admin-user-section mb-3">
            <div class="card-header"><strong>Active moderation</strong></div>
            <div class="card-body"><p class="mb-2">{{ $activeBan->reason }}</p><a href="/admin/users/{{ $subject->id }}/moderation">View moderation history</a></div>
          </section>
        @endif
        @if(trim((string)($subject->blurb ?? '')) !== '')
          <section class="card admin-user-section mb-3">
            <div class="card-header"><strong>Profile blurb</strong></div>
            <div class="card-body">{{ $subject->blurb }}</div>
          </section>
        @endif
@elseif($section==='account')
<div class="card"><div class="card-header font-weight-bold">Account details</div><div class="card-body">
<form method="POST" action="/admin/users/{{ $subject->id }}/account">@csrf
<div class="form-group"><label>Username</label><input class="form-control" readonly value="{{ $subject->username }}"><small class="text-muted">Changing usernames requires a separate audited process.</small></div>
<div class="form-group"><label>Email address</label><input class="form-control" type="email" name="email" value="{{ old('email',$subject->email) }}" required maxlength="255"></div>
<div class="form-group"><label>Account role</label><input class="form-control" value="{{ ucfirst($subject->status) }}" readonly></div>
<button class="btn btn-outline-secondary">Save email</button></form>
</div></div>
@elseif($section==='moderation')
<div class="card mb-3"><div class="card-header font-weight-bold">Moderation action</div><div class="card-body">
<form action="/admin/users/{{ $subject->id }}/moderation" method="POST">@csrf
<div class="row"><div class="col-md-6 form-group"><label>Action</label><select name="action" class="form-control"><option value="warn">Warn user</option><option value="ban">Ban user</option>@if($activeBan)<option value="unban">Lift active ban</option>@endif<option value="restrict-chat">Restrict from sending messages</option><option value="restrict-forum">Restrict from the forum</option><option value="restrict-play">Restrict from playing</option><option value="unrestrict-chat">Lift message restriction</option><option value="unrestrict-forum">Lift forum restriction</option><option value="unrestrict-play">Lift playing restriction</option></select></div><div class="col-md-6 form-group"><label>Temporary ban length (days)</label><input type="number" name="days" class="form-control" value="7" min="1" max="3650"></div></div>
<div class="form-group"><label>Restriction length (days, empty = until lifted)</label><input type="number" name="restrict_days" class="form-control" min="1" max="3650"></div>
<div class="form-group"><label>Reason</label><textarea class="form-control" name="reason" rows="3" maxlength="1000"></textarea></div>
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="permanent" value="1" id="permanent"><label class="form-check-label" for="permanent">Permanent ban (only when banning)</label></div>
<button class="btn btn-outline-secondary">Apply moderation action</button>
</form></div></div>
<div class="card mb-3"><div class="card-header">Ban history</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Moderator</th><th>Reason</th><th>Expires</th></tr></thead><tbody>
@forelse($bans as $ban)<tr><td>{{ $ban->date }}</td><td>{{ $ban->moderator }}</td><td>{{ $ban->reason }}</td><td>{{ $ban->perm ? 'Permanent' : $ban->expire }} @if($ban->undone)(Overturned)@endif</td></tr>@empty<tr><td colspan="4">No bans.</td></tr>@endforelse
</tbody></table></div></div>
<div class="card mb-3"><div class="card-header">Restrictions</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Type</th><th>Reason</th><th>Until</th></tr></thead><tbody>
@forelse($restrictions ?? [] as $r)<tr><td>{{ $r->created_at }}</td><td>{{ \App\Services\Restrictions::TYPES[$r->type] ?? $r->type }}</td><td>{{ $r->reason }}</td><td>{{ $r->expires_at ?? 'Until lifted' }} @if($r->lifted_at)(Lifted {{ $r->lifted_at }})@endif</td></tr>@empty<tr><td colspan="4">No restrictions.</td></tr>@endforelse
</tbody></table></div></div>
<div class="card"><div class="card-header">Warning history</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Moderator</th><th>Reason</th></tr></thead><tbody>
@forelse($warnings as $warning)<tr><td>{{ $warning->date }}</td><td>{{ $warning->moderator }}</td><td>{{ $warning->reason }}</td></tr>@empty<tr><td colspan="3">No warnings.</td></tr>@endforelse
</tbody></table></div></div>
@elseif($section==='notes')
<div class="card mb-3"><div class="card-header">Add staff note</div><div class="card-body"><form method="POST" action="/admin/users/{{ $subject->id }}/notes">@csrf
<textarea name="body" class="form-control mb-2" rows="4" maxlength="5000" required placeholder="Visible to administrators only"></textarea><button class="btn btn-outline-secondary">Add private note</button></form></div></div>
@forelse($notes as $note)<div class="card mb-2"><div class="card-header">{{ $note->created_at }} · Staff #{{ $note->moderator_id }}</div><div class="card-body">{{ $note->body }}</div></div>@empty<p class="text-muted">No staff notes.</p>@endforelse
{{ $notes->links() }}
@elseif($section==='economy')
<div class="card mb-3"><div class="card-header font-weight-bold">Adjust Dius</div><div class="card-body"><form method="POST" action="/admin/users/{{ $subject->id }}/dius">@csrf
<div class="row"><div class="col-md-3 form-group"><label>Amount (+/-)</label><input type="number" class="form-control" name="amount" required></div><div class="col-md-9 form-group"><label>Reason</label><input class="form-control" name="reason" required maxlength="500"></div></div><button class="btn btn-outline-secondary">Apply adjustment</button></form></div></div>
<div class="card"><div class="card-header">Recent transactions</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Asset</th></tr></thead><tbody>
@forelse($transactions as $transaction)<tr><td>{{ $transaction->date }}</td><td>{{ $transaction->type }}</td><td><img src="/s/img/diu_16.png" alt="" width="16"> {{ number_format($transaction->amount) }}</td><td>{{ $transaction->assetid }}</td></tr>@empty<tr><td colspan="4">No transactions.</td></tr>@endforelse
</tbody></table></div></div>
@elseif($section==='avatar')
<div class="card"><div class="card-header">Avatar</div><div class="card-body"><p>View and inspect the user's currently rendered avatar.</p><a href="/user/{{ $subject->id }}" class="btn btn-outline-secondary btn-sm">Open profile</a></div></div>
@elseif($section==='messages')
<div class="card admin-user-section mb-3">
  <div class="card-header"><strong>Send staff message</strong></div>
  <div class="card-body">
    <p class="text-muted mb-3">Send a private inbox message to {{ $subject->username }}. It will appear as a normal message from your staff account.</p>
    <form method="POST" action="/admin/users/{{ $subject->id }}/messages">
      @csrf
      <div class="form-group">
        <label for="staff-message-subject">Subject</label>
        <input id="staff-message-subject" class="form-control" type="text" name="subject" maxlength="255" minlength="3" value="{{ old('subject') }}" placeholder="Subject" required>
      </div>
      <div class="form-group">
        <label for="staff-message-body">Message</label>
        <textarea id="staff-message-body" class="form-control" name="body" rows="6" minlength="3" maxlength="8192" placeholder="Type your message..." required>{{ old('body') }}</textarea>
      </div>
      <button type="submit" class="btn btn-outline-secondary"><i class="far fa-envelope mr-1" aria-hidden="true"></i>Send staff message</button>
    </form>
  </div>
</div>
<div class="card admin-user-section">
  <div class="card-header"><strong>Inbox overview</strong></div>
  <div class="card-body"><strong>{{ number_format($messages) }}</strong> messages sent or received by this account. Existing messages remain private.</div>
</div>
@elseif($section==='changes')
<div class="card"><div class="card-header">Staff activity</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Moderator</th><th>Action</th><th>Details</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td>{{ $log->created_at }}</td><td>#{{ $log->moderator_id }}</td><td>{{ $log->action }}</td><td>{{ $log->details }}</td></tr>@empty<tr><td colspan="4">No staff actions recorded.</td></tr>@endforelse
</tbody></table></div></div>
@endif
    </div>
    <aside class="col-lg-3 admin-user-side">
      <div class="card admin-user-menu">
        <div class="card-header"><strong>User management</strong></div>
        <nav class="admin-user-navigation" aria-label="User management">
          <div class="admin-user-nav-group">
            @foreach(['overview'=>'Overview','account'=>'Account','avatar'=>'Avatar','economy'=>'Dius & transactions'] as $key=>$label)
              <a class="admin-user-nav-link {{ $section===$key ? 'active' : '' }}" href="/admin/users/{{ $subject->id }}{{ $key==='overview' ? '' : '/'.$key }}" @if($section===$key)aria-current="page"@endif>
                <i class="fa-fw {{ ['overview'=>'fas fa-home','account'=>'fas fa-user-cog','avatar'=>'far fa-user-circle','economy'=>'fas fa-coins'][$key] }}" aria-hidden="true"></i>
                <span>{{ $label }}</span>
              </a>
            @endforeach
          </div>
          <div class="admin-user-nav-separator" aria-hidden="true"></div>
          <div class="admin-user-nav-group">
            @foreach(['moderation'=>'Moderation','notes'=>'Staff notes','messages'=>'Messages','changes'=>'Staff history'] as $key=>$label)
              <a class="admin-user-nav-link {{ $section===$key ? 'active' : '' }}" href="/admin/users/{{ $subject->id }}/{{ $key }}" @if($section===$key)aria-current="page"@endif>
                <i class="fa-fw {{ ['moderation'=>'fas fa-gavel','notes'=>'far fa-sticky-note','messages'=>'far fa-envelope','changes'=>'fas fa-history'][$key] }}" aria-hidden="true"></i>
                <span>{{ $label }}</span>
              </a>
            @endforeach
          </div>
        </nav>
      </div>
      <p class="admin-user-menu-return"><a href="/admin/users"><i class="fas fa-chevron-left mr-1" aria-hidden="true"></i> All users</a></p>
    </aside>
  </div>
</div>
@endsection
