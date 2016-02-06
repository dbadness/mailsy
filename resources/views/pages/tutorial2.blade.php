@extends('layouts.master')

@section('content')

	<div class='jumbotron'>
		<h1>Creating email templates</h1>
		<p>Mailsy works by creating email templates that have a message that you can use over and over again for a
		given purpose. Take a look at the one below and notice the pieces of information that start with '@@'. Those are the 
		pieces of information that you can change for each recipient.</p>
		<textarea style='resize:none;width:100%;height:210px;padding:10px;border-radius:3px;border:solid 1px lightgray;' disabled>Hi @@name,

Name is Alex and we met last night at the event and spoke briefly about @@conversationTopic. I thought we had a great conversation
and wanted to follow up on that. Could we set up a time to speak sometime this week?

Thank you for your time and let me know when you'd like to connect and I'd be happy to block it out.

Best,
Alex</textarea>
		<br>
		<br>
		<a href='/tutorial/step3'><button class='btn btn-primary' style='float:right;' role='button'>Add Some Recipients!</button></a>
		<div class='clear'></div>
	</div>

@endsection