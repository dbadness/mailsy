@extends('layouts.master')

@section('content')

	@if($_GET)
		@if($_GET['message'] == 'success')
			<p style='color:green;'>Emails successfully sent.</p>
		@endif
	@endif
	
	Template:<br>
	<textarea name='template' disabled style='width:500px;height:200px;resize:none;'>{!! $data['email']->template !!}
	</textarea>

	<!-- Set up the message list -->
	<div id='messages'>
		<div class='message'>
			<div class='field'>
				Recipient
			</div>
			<div class='field' style='width:500px;'>
				Message
			</div>
			<div class='field'>
				Status
			</div>
			<div class='clear'></div>
		</div>
	</div>

	@if($data['messages'])
		<div id='messages'>
			@foreach($data['messages'] as $message)
				<div class='message'>
					<div class='field'>
						{!! $message->recipient !!}
					</div>
					<div class='field' style='width:500px;'>
						{!! substr($message->message,0,50) !!}...
					</div>
					<div class='field'>
						{!! ucfirst($message->status) !!}
					</div>
					<div class='clear'></div>
				</div>
			@endforeach
		</div>
	@endif

@endsection