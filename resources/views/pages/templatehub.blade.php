@extends('layouts.master')

@section('content')
	<br>
	<br>

	@if($user->paid && ($user->has_users == "yes" || $user->belongs_to != null))
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
				@if($compEmails != '[]')
					@foreach($compEmails as $email)
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

		@if($compEmails == '[]')

			<div class="alert alert-info" role="alert">
				No templates found. <a href='/create' class='alert-link'>Create a template</a> or <a href='/home' class='alert-link'>add one to the hub</a>!
			</div>

		@endif
	@endif

	<h4>Public Hub</h4>
	<div class="panel panel-success">
		<!-- Table -->
		<table class="table">
			<tr>
				<td><b>Template Name</b></td>
<!--
				<td class='emailListRight'><b>Copies Made</b></td>
-->
			</tr>
			@if($pubEmails != '[]')
				@foreach($pubEmails as $email)

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

	@if($pubEmails == '[]')

		<div class="alert alert-info" role="alert">
			No templates found. <a href='/create' class='alert-link'>Create a template</a> or <a href='/home' class='alert-link'>add one to the hub</a>!
		</div>

	@endif


@endsection