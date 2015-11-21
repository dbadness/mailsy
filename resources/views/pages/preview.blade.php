@extends('layouts.master')

@section('content')

	<a href='/edit/{!! base64_encode($email->id) !!}'><button>Make Some Edits</button></a>

	@foreach($messages as $message)

		<div style='border:solid 1px black;padding:10px;'>
			To: {!! $message->recipient !!}
			<br>
			Subject: {!! $message->subject !!}
			<br>
			{{ $message->message }}
		</div>

	@endforeach

@endsection