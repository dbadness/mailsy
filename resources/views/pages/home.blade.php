@extends('layouts.master')

@section('content')

	<p>Signed in as {!! $user->email !!}.</p>
	<p><a href="/logout">Log out</a></p>
	<p><a href="/newEmail">Create a new email</a></p>

@endsection