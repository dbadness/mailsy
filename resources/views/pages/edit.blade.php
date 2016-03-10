@extends('layouts.master')

@section('content')

	<script>
		// fill in the #emailTemplate
		var template = '{!! addslashes($email->template) !!}';
	</script>

	@if($_GET)
		@if($_GET['badEmails'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				At least one email is bad
			</div>
		@endif
	@endif

	<div class="page-header">
		<h1>Edit Template <small>{!! $email->name !!}</small></h1>
		<a href='/use/{!! base64_encode($email->id) !!}'>Use Template</a>
	</div>
	@if($email->temp_recipients_list)
		<form method='post' action='/makePreviews' id='makePreviews' enctype="multipart/form-data">
	@else
		<form method='post' action='/saveTemplate' enctype="multipart/form-data">
	@endif
		{!! Form::token() !!}
		<input type='hidden' name='_email_id' value='{!! $email->id !!}'>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Template Name</span>
			<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3" value='{!! $email->name !!}'>
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4" value="{!! $email->subject !!}">
		</div>
		<br>
		<div id="emailTemplate"></div>
		@if($email->temp_recipients_list)
			<div id='checkHolders'>
				<div class="btn btn-primary" id='refreshFields' style='display:inline;' role="button">Save Template and Refresh Fields</div>
				<div class='checkHolder' id='sfHolder'>
					<p>Send to Salesforce: <input type='checkbox' name='_send_to_salesforce'></p>
				</div>
				<div class='checkHolder' id='sigHolder'>
					<p>Attach Signature: <input type='checkbox' name='_signature'></p>
				</div>
				@if(!$user->sf_address || !$user->signature)
					<div class='checkHolder'>
						<p style='font-size:80%;'>Head to <a href='/settings'>the settings page</a> to add your signature and Salesforce email address</p>
					</div>
				@endif
				<div class='clear'></div>
			</div>
			<br>
			<?php $recipients = json_decode($email->temp_recipients_list); ?>
			<table class="table" id="recipientList">
				<tr id='headers'>
					<td class='field'>
						<b>Email</b>
					</td>
					@if($recipients)
						@foreach(json_decode($recipients[0]->_fields) as $k => $v)
							@foreach($v as $field => $value)
								<td class='field'>
									<b>{!! $field !!}</b>
								</td>
							@endforeach
						@endforeach
					@endif
				</tr>
				@foreach($recipients as $recipient)
					<tr class='recipient'>
						<td class='field'>
							<input type="text" name='_email[]' class="form-control" value='{!! $recipient->_email !!}'>
						</td>
						<?php $fields = json_decode($recipient->_fields); ?>
						@foreach($fields as $field)
							<td class='field'>
								<input type="text" name='{!! key((array)$field) !!}[]' class="form-control" value='{!! current((array)$field) !!}'>
							</td>
						@endforeach
					</tr>
				@endforeach
			</table>
			<div class="btn btn-info" id='addRecipient' role="button">
				<span class="glyphicon glyphicon-plus-sign"></span> Add Another Recipient
			</div>
			<button class="btn btn-primary" role="button" id='saveTemplate'>
				View Previews
			</button>
		@else
			<button class="btn btn-primary" role="button" id='saveTemplate'>
				Save Template
			</button>
		@endif
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
	</form>

@endsection