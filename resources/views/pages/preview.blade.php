@extends('layouts.master')

@section('content')

	<div class="page-header">
	<h1>View Email Previews <small>{!! $email->name !!}</small></h1>
	</div>

	{!! Form::open(['url' => '/sendEmails']) !!}
		@foreach($messages as $message)

			<div class="well">
				{!! Form::hidden('messages[]', $message->id) !!}
				To: {!! $message->recipient !!}
				<br>
				Subject: {!! $message->subject !!}
				<br>
				{!! $message->message !!}
			</div>

		@endforeach

		<button class="btn btn-primary" role="button">
			Send Emails
		</button>
		<a href='/edit/{!! base64_encode($email->id) !!}'>
			<div class="btn btn-info" role="button">
				Make Some Edits
			</div>
		</a>

	{!! Form::close() !!}

@endsection