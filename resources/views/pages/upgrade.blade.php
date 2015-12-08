@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src='/js/upgrade.js'></script>

@endsection

@section('content')

	<p>Upgrade to a paid account.</p>

	<div id='paymentUsers'>
		Pay for Myself: <input type='checkbox' name='myself' checked>
		<input type='hidden' name='myEmail' value='{!! $user->email !!}'>
		<br>
		<span id='addUser' style='color:blue;text-decoration:underline;'>Add Another Person</span>
		<br>
		<form id='otherUsers' class="input-group">
		</form>
		<div id='addUsers'>Pay for Others as Well</div>
	</div>

	<form method='post' action='/upgrade'>
		<button id="customButton" class="btn btn-primary" role="button">Subscribe to Mailsy</button>
	</form>

@endsection