@extends('layouts.master')

@section('content')

	<div style='height:200px;'></div>
	<img src='/images/500-image.png' width='500px' alt='500' style='float:left;border-radius:5px;'>
	<div class='jumbotron' style='height:500px;float:right;width:500px;'>
		<h3>We need to break out the tools.</h3>
		<p>
			Something broke on you... so sorry! If you wouldn't mind letting us 
			know what happened when Mailsy broke that'd really help us fix it.
		</p>
		<form method='post' action='/sendFeedback'>
			{!! Form::token() !!}
			<textarea style='resize:none;width:100%;height:150px;' placeholder='I was trying to...' name='feedback'></textarea>
			<br>
			<br>
			<button class='btn btn-primary'>Send Feedback</button>
			<a href='/home'><div class='btn btn-primary' role='button'>Go Home</div></a>
		</form>
	</div>
	<div class='clear'></div>

@endsection