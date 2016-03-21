@extends('layouts.master')

@section('content')

	<div class="jumbotron">
		<h1>{!! $customer->company_name !!} loves Mailsy.</h1>
		<p>{!! $customer->company_name !!} has signed up for Mailsy so you can prospect more effectively. Mailsy enables you to send better emails faster to increase the size and quality of your sales funnel. With Mailsy, you'll have better conversations to close more deals.</p>
		<p>Click on the signup button below to use one of the Mailsy licenses they purchased for you so you can use the paid version of Mailsy.</p>
		<br>
		<p><a class="btn btn-primary btn-lg" href="/auth/license" role="button">Signup</a></p>
	</div>

@endsection