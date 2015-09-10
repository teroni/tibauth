@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ $page->meta_title or $page->name }}}
@stop

{{-- Meta description --}}
@section('meta-description')
{{{ $page->meta_description }}}
@stop

{{-- Queue styles/scripts --}}
{{ Asset::queue('welcome', 'platform/less/welcome.less', 'style') }}

{{-- Page Header --}}
@section('header')

<!-- Full Width Image Header -->
<div class="caption">

	<div class="container">

		<h1>@setting('platform.app.title' )
			<span>v 1.0</span>
		</h1>

		<h3>@content('headline', 'headline.html')</h3>

	</div>

</div>

@stop

{{-- Page content --}}
@section('page')


@stop
