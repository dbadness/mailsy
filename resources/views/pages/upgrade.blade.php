@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src='/js/upgrade.js'></script>

@endsection

@section('content')

	<div class="page-header">
	  	<h1>Upgrade to a paid membership <small>Unlimited emails per day await!</small></h1>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4><strong>Reduce time prospecting while increasing the effectiveness of your cold emails with Mailsy.</strong></h4>
			<p>By signing up for a Mailsy membership, you'll be able to send an unlimited number of emails through the service per day. 
			You can either become an individual paid member or, if you're a leader on your team, you can sign your entire team up all at once making
			billing and membership administration very easy. Don't worry, you can always change these settings later!</p>
			<p>If you have any questions about billing, membership administration, or anything else, please visit the <a href='/faq'>FAQ page</a>
			or send an email to <a href="mailto:hello@mailsy.co">hello@mailsy.co</a> and you'll get a speedy response.</p> 
		</div>
		<div class="panel-body">
			<div id='paymentUsers'>
				<input type='hidden' name='myEmail' value='{!! $user->email !!}'>
				<input type='checkbox' name='myself' id='myselfCheckbox'>
				{!! Form::token() !!}
				<form method='post' action='/upgrade' id='otherUsers' class="input-group"></form>
				<div class='btn btn-info' id='myselfButton'>
					Pay for Myself
				</div>
				<div id='addUsers' class="btn btn-info">Pay for Others as Well</div>
				<button id="customButton" class="btn btn-primary" role="button">Upgrade</button>
			</div>
		</div>
	</div>


@endsection