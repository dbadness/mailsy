@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src='/js/upgrade.js'></script>

@endsection

@section('content')

	<!-- set the stripe token -->
	<input type='hidden' id='stripeKey' value="{!! env('STRIPE_P_TOKEN') !!}">

	<div class='page-header'>
		<h1>Create a Team</h1>
	</div>
	<p>When you create a team on Mailsy, you'll get a custom page at the URL below and your employees can head straight there to use the licenses that you pay for. You can always add or delete licenses at any time on your <a href='/settings'>Settings page</a> after you set your team up.</p>
	<br>
	<div class="panel panel-default">
		<div class='panel-body'>
			<form method='post' action='/createTeam' id='createTeamForm'>
				<input type='hidden' id='userEmail' value='{!! $user->email !!}'>
				{!! Form::token() !!}
				<p>Company Name:</p>
				<div class="input-group" style='width:100%;'>
					<input type="text" class="form-control" name='company_name'>
				</div>
				<br>
				<p>Custom Mailsy URL:</p>
				<div class="input-group" style='width:100%;'>
				  	<input type="text" class="form-control" value="{{ env('DOMAIN') }}/team/{!! $domain !!}" disabled>
				</div>
				<br>
				<p>Number of Users:</p>
				<div class="input-group">
				  	<input type="text" class="form-control" name='user_count'>
				</div>
				<br>
				<button class='btn btn-primary' id='createTeamButton'>Pay and Create Team</button>
		</div>
	</div>

@endsection