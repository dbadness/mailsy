@extends('layouts.master')

@section('content')

	@if($_GET)
		@if($_GET['message'] == 'emailSent')
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Email Sent!
			</div>
		@endif
	@endif

	<form method='post' action="{{ route('sendOneEmail') }}" id='sendOneEmail' enctype="multipart/form-data">
		{!! Form::token() !!}

		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Recipient</span>
			<input type="text" id='email' class="form-control" aria-describedby="basic-addon4" name="_recipient">
		</div>
		<br>

		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4" name="_subject">
		</div>
		<br>
		<div id="emailTemplate" name="_email_template"></div>

		<div id='checkHolders'>
			<div class='checkHolder' id='sfHolder'>
				<p>Send to Salesforce: <input type='checkbox' name='_send_to_salesforce' @if($user->sf_address) checked="checked" @endif></p>
			</div>
			<div class='checkHolder' id='sigHolder'>
				<p>Attach Signature: <input type='checkbox' name='_signature' @if($user->signature) checked="checked" @endif></p>
			</div>
			@if(!$user->sf_address || !$user->signature)
				<div class='checkHolder'>
					<p>Head to <a href='/settings'>the settings page</a> to add your signature and CRM BCC email address</p>
				</div>
			@endif
			<div class='clear'></div>
		</div>
		<br>

		<button class="btn btn-primary" id="sendOneEmailBtn">Send!</button>
<!-- 		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
 -->
	</form>

@endsection
