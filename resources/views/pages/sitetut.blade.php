@extends('layouts.master')

@section('content')

	<h1>Feature List</h1>
	<div class='well'>
		<img style="width: 100%;" src="/images/useTut.png">
		<p><b>Send Email</b> sends a single email and is useful if you want to send an email tracked by mailsy's features or keep track of it through mailsy's analytics!</p>
		<p><b>Send Email to List</b> sends a mailsy email to a list of people, with the normal variables, but without saving the template. It's useful if you only want to send the email once but need to send it to many people.</p>
		<p><b>Templates</b> takes you to either create a new template (<b>New Template</b>) or a list of your saved templates (<b>Saved Templates</b>). It's best used for emails you want to send over and over again.</p>

		<hr>
		<img style="width: 100%;" src="/images/moreTut.png">
		<p><b>Dashboard</b> is where you can see all the events related to your mailsy events (people opening them, clicking through links) since your last login. If you want to see all of them, go to <b>Events</b></p>
		<p><b>Outbox</b> is where you can see all the mailsy emails you've sent.</p>
		<p><b>Settings</b> is where you set your preferences, integrations, and payments.</p>
		<p><b>Template Tutorial</b> starts this whole tutorial over again!</p>
		<p><b>Upgraded Account</b> means you're a paying customer! If not, this will take you to a place where you can upgrade.</p>
		<p><b>Logout</b> logs you out.</p>

		<hr>
		<img style="height: 400px; text-align:center;" src="/images/templateTut.png">
		<p><b>Use</b> lets you use the template to send to another list or group of people.</p>
		<p><b>Messages</b> lets you see the messages sent with this template.</p>
		<p><b>Edit</b> lets you edit the template.</p>
		<p><b>Copy</b> allows you to create another template starting from the one that already exists. It's good if you want to have two versions, A/B test, that sort of thing.</p>
		<p><b>Archive</b> removes the template from list and puts into the archives (which can be accessed from the templates page). It's good for making sure your list of templates doesn't become overcrowded.</p>

		<h2 style="text-align: center;"><b>Don't understand anything? <a href="mailto:{{ env('SUPPORT_EMAIL') }}">Contact us</a> at {{ env('SUPPORT_EMAIL') }}!</b></h2>

	</div>

@endsection