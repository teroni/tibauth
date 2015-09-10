@extends('layouts/default')

{{-- Inline Scripts --}}
@section('scripts')
@parent
@stop

{{-- Page content --}}
@section('page')

<div class="row">

	<div class="col-md-6 col-md-offset-3">

		{{-- Form --}}
		<form id="register-form" role="form" method="post" accept-char="UTF-8" autocomplete="off" data-parsley-validate>

			{{-- Form: CSRF Token --}}
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="panel panel-default">

				<div class="panel-heading">Add new auth role</div>

				<div class="panel-body">

					{{-- Role Name --}}
					<div class="form-group">

						<label class="control-label" for="name">Name</label>

						<input class="form-control" type="text" name="name" id="name" value="{{{ Input::old('name') }}}" placeholder="name"
						required
						autofocus>

					</div>

					{{-- Role Permissions --}}
					<div class="form-group">

						<label class="control-label" for="permissions">Permissions - comma separated</label>

						<input class="form-control" type="text" name="permissions" id="permissions" value="{{{ Input::old('Permissions') }}}" placeholder="Permissions"
						required
						autofocus>
					</div>

					<hr>

					{{-- Form actions --}}
					<div class="form-group">

						<button class="btn btn-primary btn-block" type="submit">Submit</button>


					</div>

				</div>

			</div>

		</form>

	</div>

</div>

@stop
