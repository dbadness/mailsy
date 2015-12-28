@extends('layouts.master')

@section('content')

	<div style='height:200px;'></div>
	<img src='/images/500-image.png' width='500px' alt='500' style='float:left;border-radius:5px;'>
	<div class='jumbotron' style='height:500px;float:right;width:500px;'>
		<h3>We're all torn up.</h3>
		<p>Looks like something broke. If you wouldn't mind, send us a note letting us know what happened when Mailsy broke on you so we can fix it as soon as possible.</p>
		<form method='post' action='/sendFeedback'>
			<textarea style='resize:none;'></textarea>
			<button class='btn btn-primary'>Send Feedback</button>
		</form>
		<a href='/home'><div class='btn btn-primary' role='button'>Go Home</div></a>
	</div>
	<div class='clear'></div>

@endsection