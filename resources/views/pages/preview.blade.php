@extends('layouts.master')

@section('PageJS')

	<script src="/js/emailSender.js"></script>

@endsection

@section('content')

	<div class="page-header">
		<h1>View Email Previews <small>{!! $email->name !!}</small></h1>
	</div>
	<a class="btn pull-right" href="#sendButton"><span>Go To Bottom of Page</span></a>
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

	{!! Form::hidden('email_id', $email->id) !!}

	{!! Form::token() !!}

	@foreach($messages as $message)

		<div class="well">
			<input type='hidden' name='messages[]' class='messages' value='{!! $message->id !!}'>
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
	<a href='/edit/{!! base64_encode($email->id) !!}/withData'>
		<div class="btn btn-info" role="button">
			Make Some Edits
		</div>
	</a>
	<a href='/use/{!! base64_encode($email->id) !!}'>
		<div class="btn" role="button">
			Cancel
		</div>
	</a>

	<!-- Modal -->
	<div class="modal fade" id="emailModal" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">You're emails are being sent to their recipients!</h4>
				</div>
				<div class="modal-body">
					<!-- Toggle the view based on how many emails they are sending -->
					<strong>Sending Emails: <span id='progressText'>0%</span></strong>
					<div class="progress">
						<div class="progress-bar" style="width:0%;"></div>
					</div>
				</div>
				<div class="modal-footer" id='closeEmailModal' style='display:none;'>
					<button type="button" class="btn btn-default" data-dismiss="modal" id='closeEmailModalButton'>Close</button>
				</div>
			</div>
		</div>
	</div>

@endsection