@extends('layouts.master')

@section('pageJS')

<!-- for the text editor -->
<link href="/css/summernote.css" rel="stylesheet">
<script src="/js/summernote.js"></script>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">

@endsection

@section('content')

	<div class="page-header">
		<h1>Create a new email template</h1>
		<p>Use a single word starting with two '@' symbols to denote piece of information that you want to individualize the in the emails.</p>
		<p>Try something like:</p>
		<ul>
			<li>Hello @@name!</li>
			<li>I've read a lot about @@company lately</li>
			<li>I ran into you at @@meetingPlace and I'd like to continue that conversation</li>
		</ul>
		<p>Check out the <a href='/faq'>quick start guide</a> if you'd like to see an example!</p>
	</div>
	<form method='post' action='/makePreviews'>
		{!! Form::token() !!}
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Template Name (so you can use it later):</span>
			<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3">
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" name='_subject' class="form-control" aria-describedby="basic-addon4">
		</div>
		<br>
		<div id="emailTemplate"></div>
		<br>
		Send to Salesforce:
		<input type='checkbox' name='_send_to_salesforce'>
		<br>
		Include Signature:
		<input type='checkbox' name='_signature'>
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