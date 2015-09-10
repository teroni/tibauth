@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
:: Manage Role
@stop


{{-- Queue styles/scripts --}}
{{ Asset::queue('welcome', 'platform/less/welcome.less', 'style') }}



{{-- Page content --}}
@section('page')

<div class="row panel panel-default">
<div class="col-md-12">
<div class="col-md-6">
<h3 class="pull-left">{{ $role->name }}</h3>
</div>
<div class="col-md-6">
<br>
<p>
@if($role->authgroup)
<a href="{{ URL::to('irc/claimchan/'. $role->slug) }}" class="pull-right btn btn-default btn-sm">Reclaim Channel</a>
@endif
</p>
</div>
</div>
<div class="col-md-6">

<form class="form-horizontal small">

<br>
  <div class="form-group">
    <label for="inputPassword3" class="col-sm-2 control-label">Users</label>
    <div class="col-sm-10">
      <pre class="">{{ count($role->users) }}</pre>
    </div>
  </div>
</form>
</div>

<div class="col-md-6">
@if($role->authgroup)
<form class="form-horizontal small">
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Auth Group Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="inputEmail3" value="{{ $role->authgroup->name }}">
    </div>
  </div>
  <div class="form-group">
    <label for="inputPassword3" class="col-sm-2 control-label">ACL</label>
    <div class="col-sm-10">
      <pre class="form-control">{{ $role->authgroup->acl }}</pre>
    </div>
  </div>
  <div class="form-group">
    <label for="inputPassword3" class="col-sm-2 control-label">IRC Channels</label>
    <div class="col-sm-10">
      <pre class="form-control">{{ $role->authgroup->channels }}</pre>
    </div>
  </div>
</form>
@elseif(Sentinel::getUser()->hasAccess('superuser'))
<a href="{{ URL::to('roles/genauthgroup/' . $role->id) }}">Generate an auth group</a>
@endif
</div>
</div>

<br><br>
<!-- List Members -->
<table class="table table-striped">
      <thead>
        <tr>
          <th>User</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
              <tr class="success">
        <td>
<h4>Current Members</h4></td><td></td><td></td>
        </tr>
        @foreach($role->users()->orderBy('owner', 'desc')->orderBy('moderator', 'desc')->get() as $user)
        <tr>
            <td>{{ $user->first_name }}
            @if($user->pivot_owner)
<span class="badge">Owner</span>
            @elseif($user->pivot_moderator)
<span class="badge">Director</span>
            @endif
            </td>
            <td>{{ $user->pivot_created_at }}</td>
            <td><a href="{{ URL::to('roles/kick/' . $role->id . '/'. $user->id)  }}">Kick Member</a></td>
        </tr>
        @endforeach
        <tr class="info">
        <td>
<h4>Applied Members</h4></td><td></td><td></td>
        </tr>
         @foreach($role->requests as $request)
        <tr>
            <td>{{ $request->users->first_name }}</td>
            <td>{{ $request->created_at }}</td>
            <td><a href="{{ URL::to('roles/accept/' . $role->id . '/'. $request->user_id)  }}">Accept Member</a> | <a href="{{ URL::to('roles/deny/' . $role->id . '/'. $request->user_id)  }}">Deny Member</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
@stop
