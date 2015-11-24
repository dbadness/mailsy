@extends('layouts.master')

@section('content')

	@if($data['emails'] == '[]')
		<div class="jumbotron">
			<h2>Email prospecting is so much faster with Mailsy.</h2>
			<p>Mailsy allows you to send multiple, individualized emails in seconds - 
			create a template, fill in the pieces of relevent information that you define per recipient, 
			and send out those emails all at once.</p>
			<p>Check out the <a href='/faq'>quickstart guide</a> or dive right in and <a href='/create'>create your first template!</a>
		</div>
	@endif

	<div class="panel panel-default">
		<!-- Table -->
		<table class="table">
			<tr>
				<td><b>Email Name</b></td>
				<td class='emailListRight'><b>Emails Sent</b></td>
				<td class='emailListRight'><b>Emails Read</b></td>
				<td class='emailListRight'><b>Emails Replied To</b></td>
			</tr>
			@if($data['emails'] != '[]')
				@foreach($data['emails'] as $email)

					<tr>
						<td><a href='/email/{!! base64_encode($email->id) !!}'>{!! $email->name !!}</td>
						<td class='emailListRight'>1</td>
						<td class='emailListRight'>3</td>
						<td class='emailListRight'>3</td>
					</tr>

				@endforeach
			@endif
		</table>
	</div>

	@if($data['emails'] == '[]')

		<div class="alert alert-info" role="alert">
			No emails to report yet...
			@if(!$data['user']->paid)
				<a href='/create' class='alert-link'>Create a template</a> and send up to 10 emails per today on the free account.
			@else
				<a href='/create' class='alert-link'>Create a template</a> and send unlimited emails per day since you have an upgraded account!
			@endif
		</div>

	@endif

@endsection