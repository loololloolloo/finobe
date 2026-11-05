@extends('admin.layout')
@section('title','Game servers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h3 class="font-weight-bold mb-0">Manage game servers</h3><a href="/admin" class="btn btn-sm btn-outline-secondary">Admin panel</a></div>
<div class="row mb-3">
<div class="col-md-6 mb-3"><div class="card"><div class="card-body d-flex align-items-center" style="gap:15px"><i class="fas fa-server fa-2x text-primary"></i><div><div class="small text-muted">Registered servers</div><strong class="h4 mb-0">{{ number_format($summary['total']) }}</strong></div></div></div></div>
<div class="col-md-6 mb-3"><div class="card"><div class="card-body d-flex align-items-center" style="gap:15px"><i class="fas fa-signal fa-2x text-success"></i><div><div class="small text-muted">Active servers</div><strong class="h4 mb-0">{{ number_format($summary['active']) }}</strong></div></div></div></div>
</div>
<div class="card mb-4"><div class="card-header font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Add a game server</div>
<div class="card-body"><form method="POST" action="/admin/servers">@csrf
<div class="row">
<div class="col-md-4 form-group"><label for="ip">IP address</label><input class="form-control" id="ip" name="ip" value="{{ old('ip') }}" required></div>
<div class="col-md-2 form-group"><label for="port">Port</label><input type="number" min="1" max="65535" class="form-control" id="port" name="port" value="{{ old('port') }}" required></div>
<div class="col-md-3 form-group"><label for="placeid">Place ID</label><input type="number" min="1" class="form-control" id="placeid" name="placeid" value="{{ old('placeid') }}" required></div>
<div class="col-md-3 form-group"><label for="status">Status</label><select id="status" name="status" class="form-control"><option value="starting">Starting</option><option value="active">Active</option><option value="stopped">Stopped</option></select></div>
</div>
<div class="form-group"><label for="jobId">Job ID</label><input class="form-control" id="jobId" name="jobId" value="{{ old('jobId') }}" maxlength="255" required placeholder="Unique server job identifier"></div>
<button type="submit" class="btn btn-theme">Register server</button></form></div></div>
<div class="card"><div class="card-header font-weight-bold">Server list</div><div class="table-responsive">
<table class="table table-striped table-sm mb-0"><thead><tr><th>Job ID</th><th>IP / Port</th><th>Place</th><th>Players</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($servers as $server)
<tr><td><code>{{ $server->jobId }}</code></td><td>{{ $server->ip }}:{{ $server->port }}</td><td><a href="/place/{{ $server->placeid }}">{{ $server->placeid }}</a></td><td>{{ count($server->players ?: []) }}</td><td>{{ ucfirst($server->status) }}</td>
<td><form method="POST" action="/admin/servers/{{ $server->id }}" onsubmit="return confirm('Remove this server registration?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></td></tr>
@empty<tr><td colspan="6" class="text-center text-muted py-4">No servers registered.</td></tr>@endforelse
</tbody></table></div></div>
<div class="mt-3">{{ $servers->links() }}</div>
@endsection
