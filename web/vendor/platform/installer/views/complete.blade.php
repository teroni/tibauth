@extends('platform/installer::template')

{{-- Installer title --}}
@section('title')
@parent
:: Complete
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
				<h1>Installation Successful</h1>
				<h3>your web application is ready</h3>
				<hr>
			</header>

			<a href="{{ URL::to('/') }}" class="btn btn-primary btn-lg">
				Code Well, Rock On
			</a>



		</div>
	</section>

</section>
@stop
