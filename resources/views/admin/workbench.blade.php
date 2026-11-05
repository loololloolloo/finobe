@extends('admin.layout')
@section('title', 'Admin Panel')
@section('content')
@php
    $scopes = \App\Admin\Access::scopes($actor);
    $fullAdmin = $actor->status === 'admin';
@endphp
<h3 class="font-weight-bold mb-3">Admin Panel</h3>
<div class="catalog_container">
    <div class="row">
        @if(in_array('Catalog', $scopes, true))
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/assets" class="catalog-card">
                <div class="card"><div class="card-body text-center text-success">
                    <i class="fas fa-fw fa-check-circle fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Asset Approval</p>
                </div></div>
            </a>
        </div>
        @endif
        @if(array_intersect($scopes, ['Accounts','Moderation','Access restrictions','Economy','Avatar','Messages','Staff','Audit']))
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/users" class="catalog-card">
                <div class="card"><div class="card-body text-center text-primary">
                    <i class="fas fa-fw fa-users fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Manage Users</p>
                </div></div>
            </a>
        </div>
        @endif
        @if($fullAdmin)
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/bans" class="catalog-card">
                <div class="card"><div class="card-body text-center text-danger">
                    <i class="fas fa-fw fa-ban fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Bans</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/warn" class="catalog-card">
                <div class="card"><div class="card-body text-center text-warning">
                    <i class="fas fa-fw fa-exclamation-triangle fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Warnings</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/prune-posts" class="catalog-card">
                <div class="card"><div class="card-body text-center text-danger">
                    <i class="fas fa-fw fa-comments fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Forum Moderation</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/give_dius" class="catalog-card">
                <div class="card"><div class="card-body text-center text-warning">
                    <img src="/s/img/diu_64.png" width="64" height="64" alt="" class="admin-diu-logo"><br><br>
                    <p class="mb-0">Give Dius</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/servers" class="catalog-card">
                <div class="card"><div class="card-body text-center text-primary">
                    <i class="fas fa-fw fa-server fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Servers</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/announcements" class="catalog-card">
                <div class="card"><div class="card-body text-center text-primary">
                    <i class="fas fa-fw fa-bullhorn fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Announcements</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/createxml" class="catalog-card">
                <div class="card"><div class="card-body text-center text-success">
                    <i class="fas fa-fw fa-plus-square fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Create Item</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/rbxcreatexml" class="catalog-card">
                <div class="card"><div class="card-body text-center text-success">
                    <i class="fas fa-fw fa-download fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Import Roblox Item</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/give_badges" class="catalog-card">
                <div class="card"><div class="card-body text-center text-success">
                    <i class="fas fa-fw fa-award fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Give Badges</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/changeversions" class="catalog-card">
                <div class="card"><div class="card-body text-center text-primary">
                    <i class="fas fa-fw fa-desktop fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Client Deployer</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/elections" class="catalog-card">
                <div class="card"><div class="card-body text-center text-primary">
                    <i class="fas fa-fw fa-vote-yea fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Elections</p>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/decider" class="catalog-card">
                <div class="card"><div class="card-body text-center text-warning">
                    <i class="fas fa-fw fa-question fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Oui/Non Decider</p>
                </div></div>
            </a>
        </div>
        @endif
        @if(in_array('Blog', $scopes, true))
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a href="/admin/blog" class="catalog-card">
                <div class="card"><div class="card-body text-center text-primary">
                    <i class="fas fa-fw fa-newspaper fa-4x" aria-hidden="true"></i><br><br>
                    <p class="mb-0">Blog</p>
                </div></div>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
