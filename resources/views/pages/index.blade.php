@extends('layouts.master')

@section('content')

	<p>Here is the home page.</p>
	{!! Form::open(['url' => '/auth']) !!}

		{!! Form::submit('Signup/Login with Gmail') !!}

	{!! Form::close() !!}

@endsection