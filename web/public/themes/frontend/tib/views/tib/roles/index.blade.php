@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
:: Roles
@stop


{{-- Queue styles/scripts --}}
{{ Asset::queue('welcome', 'platform/less/welcome.less', 'style') }}



{{-- Page content --}}
@section('page')

Here are your current roles and access levels.
<br><br>
<?php
$userid = Sentinel::getUser();
$user = \Tib\Models\User::find($userid->id);
$roles =  $user->roles;
$ingroup = [];
?>
<table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Role Name</th>
          <th>Access</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $role)
        <tr>
        <?php $ingroup[] = $role->id ?>
            <td>{{ $role->name }}</td>
            <td>{{ json_encode($role->permissions) }}</td>
            <td>
            @if($role->pivot->owner || $role->pivot->moderator || $user->hasAccess('superuser'))
            @if($role->open)
            <a href="{{ URL::to('roles/manage/'. $role->id) }}">Manage</a>
            @endif
            @endif
            @if($role->open)
            <a href="{{ URL::to('roles/leave/'. $role->id) }}">Leave</a>
            @endif
            </td>
        </tr>
        @endforeach
        @foreach(\Tib\Models\Role::whereNotIn('id', $ingroup)->where('open' ,'=', 1)->get() as $role)
        <tr>
        <?php $ingroup[] = $role->id ?>
            <td>{{ $role->name }}</td>
            <td>{{ json_encode($role->permissions) }}</td>
            <td>
            @if($user->hasAccess('superuser') )
            @if($role->open)
            <a href="{{ URL::to('roles/manage/'. $role->id) }}">Manage</a>
            @endif
            @endif
            @if(!$role->request)
            <a href="{{ URL::to('roles/request/'. $role->id) }}">Apply</a>
            @else
            <span class="small grey-text"><i>Applied</i></span>
            @endif
            </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @if($user->hasAccess(['superuser']))
    <a href="{{ URL::to('roles/create') }}" class='btn btn-info'>Add Role</a>
    @endif
    <br>
    Blue Alliance IDs: {{ json_encode(Config::get('tib.config.blues'))}}<br>
    Coalition Alliance IDs: {{ json_encode(Config::get('tib.config.alliances'))}}<br>
    Corps: {{ json_encode(Config::get('tib.config.corps'))}}
@stop
