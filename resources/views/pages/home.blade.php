@extends('layouts.master')

@section('content')

	<p>Signed in as {!! $user->email !!}.</p>
	<p><a href="/logout">Log out</a></p>

@endsection