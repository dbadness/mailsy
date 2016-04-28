@extends('layouts.master')

@section('content')

	<h4>Company Hub</h4>
	<div class="panel panel-success">
	<!-- Table -->
		<table class="table">
			<tr>
				<td><b>Template Name</b></td>
<!--
				<td class='emailListRight'><b>Copies Made</b></td>
-->
			</tr>
			@if($emails != '[]')
				@foreach($emails as $email)
					<tr>
						<td>
							<span><strong>{!! $email->name !!}</strong></span>
							<span class="pull-right">
								<a class="btn btn-primary" href='/view/{!! base64_encode($email->id) !!}'>view</a>
								<a class="btn btn-info" href='/copy/{!! base64_encode($email->id) !!}'>copy</a>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</span>
							</td>
<!--
						<td class='emailListRight'>
						</td>
-->
					</tr>
				@endforeach
			@endif
		</table>
	</div>

	@if($emails == '[]')

		<div class="alert alert-info" role="alert">
			No templates found. <a href='/create' class='alert-link'>Create a template</a> or <a href='/home' class='alert-link'>add one to the hub</a>!
		</div>

	@endif

	@if($emails == '[]')

		<div class="alert alert-info" role="alert">
			No templates found. <a href='/create' class='alert-link'>Create a template</a> or <a href='/home' class='alert-link'>add one to the hub</a>!
		</div>

	@endif

	<span class="pull-right">{!! $emails->render() !!}</span>
	<br>
	<a href="/archives" class="btn btn-primary">Archived templates</a>

@endsection