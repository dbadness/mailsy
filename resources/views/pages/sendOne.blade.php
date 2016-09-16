@extends('layouts.master')

@section('content')

<script src="{!! asset('/js/sendone.js') !!}"></script>

	@if($_GET)
		@if($_GET['message'] == 'emailSent')
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Email Sent!
			</div>
		@endif
	@endif

	<div class="alert alert-danger hidden" id="notAnEmail" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				There's no valid 'to' email!
	</div>

	<form method='post' action="{{ route('sendOneEmail') }}" id='sendOneEmail' enctype="multipart/form-data">
		{!! Form::token() !!}

		@if(!$user->gmail_user)

			<!-- Flag for the email sending logic -->
			<input type='hidden' name='gmail_user' value='0'>

			<!-- Enter Password Field -->
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon4">Email Password</span>
				<input type="password" id='password' class="form-control" aria-describedby="basic-addon4" name="_password">

			</div>
			<br>
		@else

			<!-- Flag for the email sending logic -->
			<input type='hidden' name='gmail_user' value='1'>

		@endif

		@if($feedback == 1)
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">To</span>
<!-- 			<input type="text" id='email' class="form-control" aria-describedby="basic-addon4" name="_recipient">
 -->
			<ul id="recipientTags" class="form-control">
			    <!-- Existing list items will be pre-added to the tags -->
			    <li>{{ env('SUPPORT_EMAIL') }}</li>
			</ul>
		</div>

		@else
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">To</span>
<!-- 			<input type="text" id='email' class="form-control" aria-describedby="basic-addon4" name="_recipient">
 -->
			<ul id="recipientTags" class="form-control">
			    <!-- Existing list items will be pre-added to the tags -->
			</ul>
		</div>

		<div id="CCShower">
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon4">CC</span>
				<ul id="CCTags" class="form-control">
			    	<!-- Existing list items will be pre-added to the tags -->
				</ul>
			</div>

			<div class="input-group">
				<span class="input-group-addon" id="basic-addon4">BCC</span>
				<ul id="BCCTags" class="form-control">
				    <!-- Existing list items will be pre-added to the tags -->
				</ul>
			</div>
			<br>
		</div>

		@endif
		<hr>

		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4" name="_subject">
		</div>
		<br>
		<div id="emailTemplate"></div>

		<div>
			Add Attachment
			<input type="file" name="_files[]" id="fileToUpload" multiple>
		</div>

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
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>

	</form>

@endsection
