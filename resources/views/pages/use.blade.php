@extends('layouts.master')

@section('content')

<form method='post' action='/makePreviews'>
	{!! Form::token() !!} 
	<input type='hidden' name='_email_id' value='{!! $email->id !!}'>
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon3">Template Name</span>
		<input type='text' class="form-control" aria-describedby="basic-addon3" disabled value='{!! $email->name !!}'>
	</div>
	<br>
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon4">Subject</span>
		<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4" disabled value='{!! $email->subject !!}'>
	</div>
	<br>
	<div class="well">
		{!! $email->template !!}
	</div>
	<div id='checkHolders'>
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
	<br>
	<table class="table" id="recipientList">
		<tr id='headers'>
			<td style='width:40px;'></td>
			<td class='field'>
				<b>Email</b>
			</td>
			@foreach(json_decode($email->fields) as $field)
				<td class='field'>
					<b>{!! $field !!}</b>
				</td>
			@endforeach
		</tr>
		<tr class='recipient'>
			<td class='removeRow'>
				<div style='height:5px;'></div>
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</td>
			<td class='field'>
				<input type="text" class="form-control" name='_email[]'>
			</td>
			@foreach(json_decode($email->fields) as $field)
				<td class='field'>
					<input type="text" class="form-control" name='{!! $field !!}[]'>
				</td>
			@endforeach
		</tr>
	</table>
	<div class="btn btn-info" id='addRecipient' role="button">
		<span class="glyphicon glyphicon-plus-sign"></span> Add Another Recipient
	</div>
	<button class="btn btn-primary" id='viewPreviews' role="button">
		View Previews
	</button>
</form>

@endsection