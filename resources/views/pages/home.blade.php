@extends('layouts.master')

@section('content')

	<p>Signed in as {!! $data['user']->email !!}.</p>
	<p><a href="/logout">Log out</a></p>
	<p><a href="/create">Create a new email</a></p>

	@if($data['emails'])
		@foreach($data['emails'] as $email)

			<div class='email'>
				<a href='/email/{!! base64_encode($email->id) !!}'>{!! $email->name !!}</a>
			</div>

		@endforeach
	@endif

@endsection