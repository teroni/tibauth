@extends('platform/installer::template')

{{-- Installer title --}}
@section('title')
@parent
:: Configuration
@stop

{{-- Inline scripts --}}
@section('scripts')
<script>
	$(document).ready(function()
	{
		$('.form-install').bootstrapValidator();
	});
</script>
@stop

{{-- Installer content --}}
@section('page')

<section class="install">

	<header>
		<div class="brand">
			<div class="brand__image">
				<img src="{{ URL::to('packages/platform/installer/img/ornery-octopus.svg') }}" alt="Ornery Octopus">
			</div>
		</div>
	</header>

	<section class="page">

		<div class="page__wrapper">

			<header>
				<h1>Installing Platform</h1>
				<h3>A flexible and extensible web application</h3>
				<hr>
			</header>

			<section class="license{{ Session::has('license') ? ' hide' : null }}">

				<p>{{{ Platform::getLicense() }}}</p>
				<hr>
				<button class="btn btn-primary btn-lg btn-license">Agree</button>

			</section>

			<section class="install__form"{!! Session::has('license') ? ' style="display: block;"' : null !!}>

				<form id="form" action="{{ Request::fullUrl() }}" method="post" class="form-install form-horizontal"
				data-bv-message="This value is not valid"
				data-bv-feedbackicons-valid="fa fa-check"
				data-bv-feedbackicons-invalid="fa fa-flag"
				data-bv-feedbackicons-validating="fa fa-refresh">

				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="checkbox" name="license" class="license-agreement">

				<fieldset>

					@if ($errors->count())
					@foreach ($errors->all(':message') as $error)
					<div class="form-group">
						<div class="alert alert-danger" role="alert"> {{ $error }} </div>
					</div>
					@endforeach
					@endif

					<div class="form-group">
						<input type="email" name="user[email]" class="form-control" value="{{{ Input::old('user.email') }}}" placeholder="Email Address" required
						data-bv-email-message="The input is not a valid email address">
					</div>

					<div class="form-group">

						<input type="password" id="password" class="form-control" name="user[password]" value="{{{ Input::old('user.password') }}}" placeholder="Password" required
						data-bv-notempty="true"
						data-bv-notempty-message="The password is required and cannot be empty"

						data-bv-identical="true"
						data-bv-identical-field="confirmPassword"
						data-bv-identical-message="The password and its confirm are not the same" />

					</div>

					<div class="form-group">

						<input type="password" id="confirmPassword" class="form-control" name="user[password_confirm]" value="{{{ Input::old('user.password_confirm') }}}" placeholder="Confirm Password" required
						data-bv-notempty="true"
						data-bv-notempty-message="The confirm password is required and cannot be empty"

						data-bv-identical="true"
						data-bv-identical-field="user[password]"
						data-bv-identical-message="The password and its confirmation are not the same" />

					</div>

					<div class="form-group">
						<select name="database[driver]" id="choose-database-driver" class="form-control" required
						data-bv-notempty="true"
						data-bv-notempty-message="Please select a database driver">
							<option class="default" value="">Database Driver</option>
							<optgroup label="Available Drivers">
								@foreach ($drivers as $driver => $config)
								<option value="{{ $driver }}" {{ (Input::old('database.driver') == $driver) ? 'selected' : '' }}>
									{{ $driver }}
								</option>
								@endforeach
							</optgroup>
						</select>
					</div>

					@foreach ($drivers as $driver => $config)
					<div class="database-driver hide" id="database-driver-{{ $driver }}">

						@foreach ($config as $index => $field)
						<div class="form-group">
							<input class="form-control" type="{{ $index == 'password' ? 'password' : 'text' }}" name="database[{{ $driver }}][{{ $index }}]" placeholder="{{ $index }}" value="{{{ Input::old("database.{$driver}.{$index}", $field['value']) }}}"{{{ $field['rules'] }}}>
						</div>
						@endforeach

					</div>
					@endforeach


					<button type="submit" class="btn btn-primary btn-lg">Install</button>

				</fieldset>

			</form>

		</section>

	</div>

</section>

@stop
