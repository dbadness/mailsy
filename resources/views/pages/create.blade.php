@extends('layouts.master')

@section('content')
<br>
<br>
	<!-- error reporting -->
	<div class="alert alert-danger alert-dismissible" id='noContent' role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		Please make sure you at least have a name, subject, and a body for this template.
	</div>
	@if($errors->any())
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{!! $errors->first() !!}
		</div>
	@endif
	<div class="page-header">
		@if($emails == 0)
			<h1>Welcome to Mailsy!</h1>
			<h2>Let's get started with your first email template.</h2>
		@else
			<h1>New Email Template</h1>
		@endif
		
		<p>Use a single word starting with two '@' symbols to denote a piece of information that you want to individualize the in the emails. (Yes, you can use punctuation!)</p>
		<p>Try something like:</p>
		<ul>
			<li>Hello @@name!</li>
			<li>I noticed that you purchased @@product and I was hoping...</li>
			<li>We had a conversation about @@topic at the event last night...</li>
		</ul>
		<p><b>*Please Note* You can't have two different fields with the same name like "Today is @@day and tomorrow is @@day".</b></p>
		<p>Check out the <strong><a href='/faq'>quick start guide</a></strong> if you'd like to see an example!</p>
	</div>
	<form method='post' action='/makePreviews' id='makePreviews' enctype="multipart/form-data">
		{!! Form::token() !!}
			
		<div class="input-group" style="border-style: solid;">
			<span class="input-group-addon" id="basic-addon3" style="border: solid;">Template Name</span>
			<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3">
		</div>

		<br>
		
		<div class="input-group" style="border-style: solid;">
			<span class="input-group-addon" id="basic-addon4" style="border: solid;">Subject</span>
			<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4">
		</div>
		
		<br>
		<div id="emailTemplate"></div>
		<div id='checkHolders'>
			<div class="btn btn-primary" id='addContacts' role="button">Save Template and Add Contacts</div>
			<div class="btn btn-primary" id='refreshFields' role="button">Save Template and Refresh Fields</div>
			<div class='checkHolder' id='sfHolder'>
				<p>Send to Salesforce: <input type='checkbox' name='_send_to_salesforce'></p>
			</div>
			<div class='checkHolder' id='sigHolder'>
				<p>Attach Signature: <input type='checkbox' name='_signature'></p>
			</div>
			@if(!$user->sf_address || !$user->signature)
				<div class='checkHolder'>
					<p>Head to <strong><a href='/settings'>the settings page</a></strong> to add your signature and Salesforce email address</p>
				</div>
			@endif
			<div class='clear'></div>
		</div>
		<br>
		<div class="alert alert-success alert-dismissible" id='saved' role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Hooray!</strong> Template saved.
		</div>
		<br>
		<div id='fields'>
			<table class="table" id="recipientList">
			</table>
			<div class="btn btn-info" id='addRecipient' role="button">
				<span class="glyphicon glyphicon-plus-sign"></span> Add Another Recipient
			</div>
			<button class="btn btn-primary" id='viewPreviews' role="button">
				View Previews
			</button>

			<br>
			<br>
			<p><b>And/or upload a CSV</b>
				<input type="file" name="csvFile" id="csvFileUpload" accept=".csv" value="" />
			</p>

		</div>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>

	</form>

@endsection