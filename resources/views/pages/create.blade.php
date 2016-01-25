@extends('layouts.master')

@section('content')
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
		<h1>Create a new email template</h1>
		<p>Use a single word starting with two '@' symbols to denote a piece of information that you want to individualize the in the emails. (Yes, you can use punctuation!)</p>
		<p>Try something like:</p>
		<ul>
			<li>Hello @@name!</li>
			<li>I noticed that you purchased @@product and I was hoping...</li>
			<li>We had a conversation about @@topic at the event last night...</li>
		</ul>
		<p>*Please Note* You can't have two different fields with the same name like "Today is @@day and tomorrow is @@day".</p>
		<p>Check out the <a href='/faq'>quick start guide</a> if you'd like to see an example!</p>
	</div>
	<form method='post' action='/makePreviews' id='makePreviews'>
		{!! Form::token() !!}
		@if($emails == 0)
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon3">Template Name</span>
				<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3" value="My First Template">
			</div>
		@else
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon3">Template Name</span>
				<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3">
			</div>
		@endif

		<br>
		
		@if($emails == 0)
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon4">Subject</span>
				<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4" value="Working with @@company">
			</div>
		@else
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon4">Subject</span>
				<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4">
			</div>
		@endif
		
		<br>
		@if($emails == 0)
			<div id="emailTemplate">
				Hi @@name,
				<br><br>
				My name is Alex and we spoke at the conference last week. After some thought, I wanted to follow up about our conversation about @@topic
				and see if there was a chance that our two companies could work together.
				<br><br>
				Let me know if you'd like to connect this week and I'd be happy throw out some ideas about how a partnership could help us both.
				<br><br>
				Best,
				<br>
				Alex
			</div>
		@else
			<div id="emailTemplate"></div>
		@endif
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
					<p>Head to <a href='/settings'>the settings page</a> to add your signature and Salesforce email address</p>
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
		</div>
		@if($emails == 0)
			<div id='firstEmail' class='jumbotron'>
				<h3>Send an example email to yourself to see how it looks!</h3>
				<br><br>
				<table class="table" id="recipientList">
					<tr id='headers'>
						<td class='field'><b>email</b></td>
						<td class='field'><b>company</b></td>
						<td class='field'><b>name</b></td>
						<td class='field'><b>topic</b></td>
					</tr>
					<tr>
						<td class='field'><input type="text" name="first-email" class="form-control" value='{!! $user->email !!}'></td>
						<td class='field'><input type="text" name="first-company" class="form-control" value="Example, Inc"></td>
						<td class='field'><input type="text" name="first-name" class="form-control" value='Steve'></td>
						<td class='field'><input type="text" name="first-topic" class="form-control" value='getting more users to your site'></td>
					</tr>
				</table>
				<div class="btn btn-info" style='float:left;' id='sendFirstEmail' role="button">
					Send Test Email to Myself
				</div>
				<div style='float:left;margin-left:20px;display:none;' id='firstEmailSending'>
					<img src='/images/ring.gif' width='30px' alt='Loading'>
				</div>
				<div style='float:left;margin-left:20px;display:none;' id='firstEmailSent'>
					<h4>Email sent!</h4>
				</div>
				<div class='clear'></div>
			</div>
		@endif
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
	</form>

@endsection