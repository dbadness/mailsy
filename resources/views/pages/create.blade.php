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
		<p>Use a single word starting with two '@' symbols to denote a piece of information that you want to individualize the in the emails.</p>
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
						<input type="text" name='_email' class="form-control">
					</td>
				</tr>
			</table>
			<div class="btn btn-info" id='addRecipient' role="button">
				<span class="glyphicon glyphicon-plus-sign"></span> Add Another Recipient
			</div>
			<div class="btn btn-primary" id='viewPreviews' role="button">
				Save and View Previews
			</div>
		</div>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
	</form>

@endsection