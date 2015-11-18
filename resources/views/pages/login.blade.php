@extends('layouts.master')

@section('content')

{!! Form::open(['url' => '/auth']) !!}

	{!! Form::submit('Signup/Login with Gmail') !!}

{!! Form::close() !!}

@endsection