@extends('layouts.master')

@section('content')

	<a href='/edit/{!! base64_encode($email->id) !!}'><button>Make Some Edits</button></a>

	{!! Form::open(['url' => '/sendEmails']) !!}
		@foreach($messages as $message)

			<div style='border:solid 1px black;padding:10px;'>
				{!! Form::hidden('messages[]', $message->id) !!}
				To: {!! $message->recipient !!}
				<br>
				Subject: {!! $message->subject !!}
				<br>
				{!! $message->message !!}
			</div>

		@endforeach

		{!! Form::submit('Send Emails') !!}

	{!! Form::close() !!}

@endsection