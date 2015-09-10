@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
:: API - Add
@stop


{{-- Queue styles/scripts --}}
{{ Asset::queue('welcome', 'platform/less/welcome.less', 'style') }}



{{-- Page content --}}
@section('page')
<h3>Add a full API here: <a href="http://community.eveonline.com/support/api-key/CreatePredefined?accessMask=1073741823" target="_blank">create predefined</a></h3>

<form action='{{ URL::to('auth/addapi') }}' method="post">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
  <div class="form-group">
    <label for="keyid">KeyID</label>
    <input type="text" class="form-control" id="keyid" name="keyid" placeholder="KeyID">
  </div>
  <div class="form-group">
    <label for="vcode">vCode</label>
    <input type="text" class="form-control" id="vcode" name="vcode" placeholder="vCode">
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>
@stop
