@extends('layouts.master')

@section('content')

	<div class='jumbotron'>
		<h1>Welcome to Mailsy!</h1>
		<p>Mailsy allows you to send dozens of personalized emails to customers, prospects, colleagues, or whomever you decide quickly and authentically.</p>
		<ul>
			<li><strong>Mailsy saves time</strong> - Mailsy's templating technology allows you build a message that you can use over and over again.</li>
			<li><strong>Mailsy is personal</strong> - Assign pieces of information in the template that you want to individualize for each recipient.</li>
			<li><strong>Mailsy is authentic</strong> - Mailsy emails come right from your email address, not from a marketing company. They can even be seen in your sent folder.</li>
			<li><strong>Mailsy is integrated</strong> - In the <a href='/settings'>settings page</a> you can add a signature and even a BCC email so your emails are tracking in your CRM system.</li>
		</ul>
		<p>First, let's make an email template that you can use to email many people at the same time.</p>
		<a href='/tutorial/step2'><button class='btn btn-primary' style='float:right;' role='button'>Let's Make a Template!</button></a>
		<div class='clear'></div>
	</div>

@endsection