@extends('layouts.master')

@section('content')

	<div style='height:200px;'></div>
	<img src='/images/500-image.png' width='500px' alt='400' style='float:left;border-radius:5px;'>
	<div class='jumbotron' style='height:500px;float:right;width:500px;'>
		<h3>Hmm... we're having some trouble.</h3>
		<p>Even the best in the world lose their way sometimes. No worries, let's get back on track.</p>
		<a href='/home'><div class='btn btn-primary' role='button'>Go Home</div></a>
	</div>
	<div class='clear'></div>

@endsection