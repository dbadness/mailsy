@extends('layouts.master')

@section('content')

	<p>Edit '{!! $email->name !!}':</p>
	<form method='post' action='/updatePreviews'>
		{!! Form::token() !!}
		<input type='hidden' name='_email_id' value='{!! $email->id !!}'>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Template Name:</span>
			<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3">
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4">
		</div>
		<br>
		<div id="emailTemplate">{!! $email->template !!}</div>
		<div id='checkHolders'>
			<div class="btn btn-primary" id='addContacts' role="button">Add Contacts</div>
			<div class='checkHolder' id='sfHolder'>
				<p>Send to Salesforce: <input type='checkbox' name='_send_to_salesforce'></p>
			</div>
			<div class='checkHolder' id='sigHolder'>
				<p>Attach Signature: <input type='checkbox' name='_signature'></p>
			</div>
			@if(!$user->sf_address || !$user->signature)
				<div class='checkHolder'>
					<p>Head to <a href='/settings'>the settings page</a> to add your signature and Salesforce email address</p>
				</div>
			@endif
			<div class='clear'></div>
		</div>
		<br><br>
		<div id='fields'>
			<table class="table" id="recipientList">
				<tr id='headers'>
					<td class='field'><b>Email</b></td>
				</tr>
				<tr id='recipient'>
					<td class='field'>
						<input type="text" name='_email[]' class="form-control">
					</td>
				</tr>
			</table>
			<div class="btn btn-info" id='addRecipient' role="button">
				<span class="glyphicon glyphicon-plus-sign"></span> Add Another Recipient
			</div>
			<button class="btn btn-primary" id='viewPreviews' role="button">
				Save and View Previews
			</button>
		</div>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
	</form>

@endsection