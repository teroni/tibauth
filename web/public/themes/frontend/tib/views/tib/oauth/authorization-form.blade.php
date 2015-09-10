@extends('layouts.default')

@section('page')
<div class="row">

    <div class="col-md-6 col-md-offset-3">

        {{-- Social Logins --}}
        <div class="panel panel-default">

            <div class="panel-heading">Oauth</div>

            <div class="panel-body">

    <div class="form-group">
        <dl class="dl-horizontal">
            <dt>Authenticating with:</dt>
            <dd>Forums</dd>
        </dl>
    </div>
    <form method="post">
      <label>Do You Authorize TestClient?</label><br />
      <input type="submit" name="authorized" value="yes">
      <input type="submit" name="authorized" value="no">
      <input type="hidden" name="state" id="" value="auth">
    </form>

            </div>

        </div>

    </div>

</div>
    
@endsection