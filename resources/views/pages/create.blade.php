@extends('layouts.master')

@section('content')

	<p>Make a new email to send out:</p>
	{!! Form::token() !!}
	<form method='post' action='/makePreviews'>
		<label for='_name'>Name:</label>
		<input name='_name'><br><br>
		<input name='_subject' id='subject'><br><br>
		<textarea id='emailTemplate'></textarea>
		<br>
		<div class='button' id='addContacts'>Add Contacts</div>
		<br><br>
		<div class='button' id='addRecipient'>Add Another Recipient</div>
		<br><br>
		<span id='loading'>Loading...</span>
		<div id='fields'>
			<div id='headers'>
				<div class='header'>
					Email
				</div>
			</div>
			<div id='recipients'>
				<div class='recipientRow'>
					<div class='field'>
						<input name='_email[]' class='fieldInput'>
					</div>
				</div>
			</div>
		</div>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
		{!! Form::token() !!}
		<br>
		<input id='viewPreviews' type='submit' value='View Previews'>
	</form>

@endsection