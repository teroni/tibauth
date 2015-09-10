@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
:: APIs
@stop


{{-- Queue styles/scripts --}}
{{ Asset::queue('welcome', 'platform/less/welcome.less', 'style') }}



{{-- Page content --}}
@section('page')

Start by entering your APIs
<br><br>
<?php
$userid = Sentinel::getUser();
$user = \Tib\Models\User::find($userid->id);
$apis =  $user->apis;

?>
<table class="table table-striped">
      <thead>
        <tr>
          <th>Character</th>
          <th>Corporation</th>
          <th>Last API Pull</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @if($apis)
      @foreach($apis as $api)
<tr>
          <th scope="row">{{ $api->charname }}</th>
          <td>{{ $api->corporation }}</td>
          <td>{{ $api->api_time }}</td>
          <td><a href="{{ URL::to('auth/removeapi/'. $api->id) }}">X</a></td>
        </tr>
@endforeach
@endif
        
      </tbody>
    </table>
<a href="{{ URL::to('auth/addapi/') }}">Add API</a><br>
Current saved APIS: {{ count($apis) }}
@stop
