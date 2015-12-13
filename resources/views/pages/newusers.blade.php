@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src='/js/upgrade.js'></script>

@endsection

@section('content')

	<div class='page-header'>
		<h1>Spread the Joy of Mailsy!</h1>
	</div>
	<div class="panel panel-default">
	<div class="panel-heading"><strong>Add New People to your Subscription</strong></div>
		<div class="panel-body">
			<div id='paymentUsers'>
				<input type='hidden' name='myEmail' value='{!! $user->email !!}'>
				<form method='post' action='/upgrade/add' id='otherUsers' class="input-group"></form>
				@if(!$user->paid)
					<input type='checkbox' name='myself' id='myselfCheckbox'>
					<div class='btn btn-info' id='myselfButton'>
						Pay for Myself
					</div>
				@endif
				<div id='addUsers' class="btn btn-info">Pay for Others</div>
				<button id="addUsersButton" class="btn btn-primary" role="button">Upgrade</button>
			</div>
		</div>
	</div>

	<div class="alert alert-info alert-dismissible" role="alert" id='confirm' style='display:none;'>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<div id='confirmPopup'></div>
	</div>

	{!! Form::token() !!}
	<input type='hidden' name='prorated_amount' id='proratedAmount' value='{!! $prorated_amount !!}'>
	<input type='hidden' name='lastFour' id='lastFour' value='{!! $lastFour !!}'>

@endsection