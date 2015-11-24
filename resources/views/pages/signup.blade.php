@extends('layouts.master')

@section('content')

	<p>Here is the signup page.</p>
	{!! Form::open(['url' => '/auth']) !!}

		{!! Form::submit('Signup with Gmail') !!}

	{!! Form::close() !!}

@endsection