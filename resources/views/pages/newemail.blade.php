@extends('layouts.master')

@section('content')

	<p>Make a new email to send out:</p>
	{!! Form::token() !!}
	<textarea id='email' style='resize:none;width:500px;height:200px;'></textarea>
	<br>
	<button id='addContacts'>Add Contacts</button>
	<br><br>
	<button id='addRecipient'>Add Another Recipient</button>
	<br><br>
	<span id='loading' style='display:none;'>Loading...</span>
	<div id='fields'>
		<div id='headers'>
			<div class='header'>
				Email
			</div>
		</div>
		<div id='recipients'>
			<div class='recipientRow'>
				<div class='field'>
					<input name='email' class='fieldInput'>
				</div>
			</div>
		</div>
	</div>

@endsection