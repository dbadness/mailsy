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
			@if($emails == 0)
				<h1>Welcome to Mailsy! Let's get started with your first email template.</h1>
			@else
				<h1>Create a new email template</h1>
			@endif
		
			<p>Use a single word starting with two '@' symbols to denote a piece of information that you want to individualize the in the emails. (Yes, you can use punctuation!)</p>
			<p>Try something like:</p>
			<ul>
				<li>Hello @@name!</li>
				<li>I noticed that you purchased @@product and I was hoping...</li>
				<li>We had a conversation about @@topic at the event last night...</li>
			</ul>
			<p><b>*Please Note* You can't have two different fields with the same name like "Today is @@day and tomorrow is @@day".</b></p>
			<p>Check out the <a href='/faq'>quick start guide</a> if you'd like to see an example!</p>
		</div>

	<form method='post' action='/createTemplate' id='makePreviews'>
		{!! Form::token() !!}
			
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Template Name</span>
			<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3">
		</div>

		<br>
		
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4">
		</div>
		
		<br>
		<div id="emailTemplate"></div>
		<div id='checkHolders'>
			<button class="btn btn-primary" id='addContacts' role="button">Save Template</button>
		</div>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
	</form>

@endsection