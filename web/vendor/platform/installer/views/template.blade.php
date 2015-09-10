<!DOCTYPE html>
<html>
<head>
	<title>
		@section('title')
		Platform Installer
		@show
	</title>

	<link href="{{ URL::to('packages/platform/installer/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ URL::to('packages/platform/installer/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ URL::to('packages/platform/installer/css/bootstrapValidator.css') }}" rel="stylesheet">
	<link href="{{ URL::to('packages/platform/installer/css/install.css') }}" rel="stylesheet">

	<script src="{{ URL::to('packages/platform/installer/js/modernizr.js') }}"></script>

	@section('styles')
	@show
</head>
<body>

	<div class="loader">
		<div>
			<span>
				<img src="{{ URL::to('packages/platform/installer/img/ornery-octopus.svg') }}" alt=""><br>Installing
				<div id="loadingbar">
					<div class="bar"></div>
				</div>
			</span>
		</div>
	</div>

	@yield('page')

	<script src="{{ URL::to('packages/platform/installer/js/jquery.js') }}"></script>
	<script src="{{ URL::to('packages/platform/installer/js/bootstrap.min.js') }}"></script>
	<script src="{{ URL::to('packages/platform/installer/js/bootstrapValidator.min.js') }}"></script>
	<script src="{{ URL::to('packages/platform/installer/js/install.js') }}"></script>

	@section('scripts')
	@show

</body>

</html>
