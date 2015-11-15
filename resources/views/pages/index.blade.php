@extends('layouts.master')

@section('content')

	<p>Here is the home page.</p>
	{!! Form::open(array('url' => '/signup')) !!}

		{!! Form::submit('Signup with Gmail') !!}

	{!! Form::close() !!}

@endsection