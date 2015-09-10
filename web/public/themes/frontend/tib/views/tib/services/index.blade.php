@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent :: Services
@stop

{{-- Queue Assets --}}
{{ Asset::queue('platform-validate', 'platform/js/validate.js', 'jquery') }}

{{-- Inline Scripts --}}
@section('scripts')
@parent
@stop

{{-- Page content --}}
@section('page')
<?php
$userid = Sentinel::getUser();

      $currentUser = \Tib\Models\User::find($userid->id);
?>
<div class="row">

  <div class="col-md-6 col-md-offset-3">

    {{-- Form --}}
    <form id="profile-form" role="form" method="post" accept-char="UTF-8" data-parsley-validate>

      {{-- Form: CSRF Token --}}
      <input type="hidden" name="_token" value="{{ csrf_token() }}">

      <div class="panel panel-default">

        <div class="panel-heading">Services</div>

        <div class="panel-body">

          {{-- TS3 ID --}}
          <div class="form-group{{ Alert::onForm('ts3id', ' has-error') }}">

            <label class="control-label" for="ts3id">TS3 Identity</label>
            <input class="form-control" type="text" name="ts3id" id="ts3id" value="{{ @$currentUser['ts3-identity'] }}" placeholder="id">
          </div>
          {{-- TS3 ID --}}
          <div class="form-group{{ Alert::onForm('ircpass', ' has-error') }}">
          <label class="control-label" for="ircpass">IRC Username</label>
          @if($currentUser->irc_pass)
          <div class="input-group">
            <pre class="form-control">{{ str_replace(' ', '', $currentUser->first_name) }}</pre>
            <a href="{{ URL::to('irc/reregister') }}" class="input-group-addon btn btn-warning">Re-Create</a>
          </div>
          
          @else
          <pre class="bg-info" style="text-align:center;">Please enter a pass and save to generate your IRC user</pre>
          @endif
            <label class="control-label" for="ircpass">IRC Password</label>
            <input class="form-control" type="password" name="ircpass" id="ircpass" placeholder="•••">
          </div>
          {{-- Form actions --}}
          <div class="form-group">

            <button class="btn btn-primary btn-block" type="submit">Submit</button>

          </div>
<a href="{{ URL::to('irc/refresh') }}" class="btn btn-success btn-block">Refresh IRC Permissions </a>
<a href="{{ URL::to('services/ts3') }}" class="btn btn-success btn-block">Refresh TS3 </a><small>Please connect to TS3 prior to refreshing or an error will occur</small>
        </div>


      </div>

    </form>

  </div>

</div>

@stop
