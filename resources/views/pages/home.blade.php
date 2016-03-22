@extends('layouts.master')

@section('content')
	<br>
	<br>

	@if($emails == '[]')
		<div class="jumbotron">
			<h2>Prospecting is lightspeed with Mailsy.</h2>
			<p>Mailsy allows you to send multiple, individualized emails in seconds - 
			create a template, fill in the pieces of relevent information, 
			and  hit send. Just once!</p>
			<p>Dive right in and <a href='/create'><strong>create your first template!</strong></a> or check out the <a href='/faq'><strong>quickstart guide</strong></a></p>
		</div>
	@endif

	<div class="panel panel-success">
		<!-- Table -->
		<table class="table">
			<tr>
				<td><b>Email Name</b></td>
				<td class='emailListRight'><b>Emails Sent</b></td>
			</tr>
			@if($emails != '[]')
				@foreach($emails as $email)
					<?php
						$messageCount = App\Message::where('email_id',$email->id)->whereNotNull('status')->whereNull('deleted_at')->count();
					?>
					<tr>
						<td>
						<form method='get' action='/archive/{!! $email->id !!}' enctype="multipart/form-data">
							<span><strong>{!! $email->name !!}</strong></span>
							<span class="pull-right">
								<a class="btn btn-primary" href='/use/{!! base64_encode($email->id) !!}'>use</a>
								<a class="btn btn-info" href='/email/{!! base64_encode($email->id) !!}'>info</a>
								<a class="btn btn-info" href='/edit/{!! base64_encode($email->id) !!}'>edit</a>
								<a class="btn btn-info" href='/copy/{!! base64_encode($email->id) !!}'>copy</a>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<button class="btn btn-danger" id='archiveEmail'>archive</button>
							</span>
						</form>
						 </td>
						<td class='emailListRight'>{!! $messageCount !!}
						</td>
					</tr>
				@endforeach
			@endif
		</table>
	</div>

	<a href="/archives">Archived templates</a>

	@if($emails == '[]')

		<div class="alert alert-info" role="alert">
			No emails to report yet...
			@if(!$user->paid)
				<a href='/create' class='alert-link'>Create a template</a> and send up to 10 emails per today on the free account.
			@else
				<a href='/create' class='alert-link'>Create a template</a> and send unlimited emails per day since you have an upgraded account!
			@endif
		</div>

	@endif

@endsection