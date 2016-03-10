@extends('layouts.master')

@section('content')

	<div class="page-header">
		<h1>View Email Previews <small>{!! $email->name !!}</small></h1>
	</div>
	<a class="btn btn-info pull-right" href="#sendButton"><span>Go To End</span></a>
	<br>
	<br>

	<!-- Let them know how many emails they have left (and that, if they send em, they'll be deleted -->
	@if(!$user->paid && (count($messages) > App\User::howManyEmailsLeft()))
		<div class="alert alert-info" role="alert">
			You have <strong>{!! App\User::howManyEmailsLeft() !!} emails left</strong> to send today on your free account but you're trying to send <strong>{!! count($messages) !!}</strong> - emails beyond the quota won't be sent.
            <br>
            <br>
            If you love Mailsy, why not <a class='alert-link' href='/upgrade'>upgrade</a> so you can send 
            unlimited emails per day?
        </div>
	@endif

	{!! Form::open(['url' => '/sendEmails']) !!}
		{!! Form::hidden('email_id', $email->id) !!}
		@foreach($messages as $message)

			<div class="well">


				{!! Form::hidden('messages[]', $message->id) !!}
				To: {!! $message->recipient !!}
				<br>
				@if($message->send_to_salesforce)
					BCC: {!! $user->sf_address !!}
					<br>
				@endif
				Subject: {!! $message->subject !!}
				<br>
				{!! $message->message !!}
			</div>

		@endforeach

		<button class="btn btn-primary" role="button" id="sendButton">
			Send Emails
		</button>
		<a href='/edit/{!! base64_encode($email->id)/withData !!}'>
			<div class="btn btn-info" role="button">
				Make Some Edits
			</div>
		</a>

	{!! Form::close() !!}

@endsection