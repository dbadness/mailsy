@extends('layouts.master')

@section('pageJS')

	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script type="text/javascript">
		// This identifies your website in the createToken call below
		Stripe.setPublishableKey('pk_test_CIZBh7IaLuncqqScIchbbbuh');
		// ...
	</script>

@endsection

@section('content')

	<p>Upgrade to a paid account.</p>

	<form action='/upgrade' method='POST' id='payment-form'>

		<div id='paymentUsers'>
			Pay for Myself: <input type='checkbox' name='myself'>
			<br>
			<span id='addUser' style='color:blue;text-decoration:underline;'>Add Another Person</span>
			<br>
			<div id='addUsersField'>
			</div>
			<div id='addUsers'>Pay for Others as Well</div>
		</div>

		<div id='ccDetails'
			<span class="payment-errors"></span>

			<div class="form-row">
				<label>
					<span>Card Number</span>
					<input type="text" size="20" data-stripe="number"/>
				</label>
			</div>

			<div class="form-row">
				<label>
					<span>CVC</span>
					<input type="text" size="4" data-stripe="cvc"/>
				</label>
			</div>

			<div class="form-row">
				<label>
					<span>Expiration (MM/YYYY)</span>
					<input type="text" size="2" data-stripe="exp-month"/>
				</label>
				<span> / </span>
				<input type="text" size="4" data-stripe="exp-year"/>
			</div>
			{!! Form::token() !!}
			<button type="submit">Submit Payment</button>
		</div>
	</form>

@endsection