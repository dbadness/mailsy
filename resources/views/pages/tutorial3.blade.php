@extends('layouts.master')

@section('content')

	<div class='jumbotron'>
		<h2>Step 3 of 3: Including Recipient Information</h2>
		<p>Now that the template is made, it's time to add some recipients and the information that makes your message relevant to them so they're
		inclined to respond. For this example, this email will be sent to you so you can see what a recipient would receive.</p>
		<div style='padding:10px;background:white;border-radius:3px;border:solid 1px lightgray;'>Working with @@company</div>
		<br>
		<textarea style='resize:none;width:100%;height:210px;padding:10px;border-radius:3px;border:solid 1px lightgray;' disabled>Hi @@name,

Name is Alex and we met last night at the event and spoke briefly about @@conversationTopic. I thought we had a great conversation
and wanted to follow up on that. Could we set up a time to speak sometime this week?

Thank you for your time and let me know when you'd like to connect and I'd be happy to block it out.

Best,
Alex</textarea>
		<br><br>
		<table class="table" id="recipientList">
			<tr id='headers'>
				<td class='field'><b>email</b></td>
				<td class='field'><b>company</b></td>
				<td class='field'><b>name</b></td>
				<td class='field'><b>conversationTopic</b></td>
			</tr>
			<tr>
				<td class='field'><div style='padding:6px;background:white;border-radius:3px;border:solid 1px lightgray;'>{!! $user->email !!}</div></td>
				<td class='field'><div style='padding:6px;background:white;border-radius:3px;border:solid 1px lightgray;'>Example Co, Inc</div></td>
				<td class='field'><div style='padding:6px;background:white;border-radius:3px;border:solid 1px lightgray;'>Steve</div></td>
				<td class='field'><div style='padding:6px;background:white;border-radius:3px;border:solid 1px lightgray;'>getting more users to your site</div></td>
			</tr>
		</table>
		<a class="btn btn-warning" style='float:right;margin:0 0 0 20px;' href='/featuretutorial'>
			<div>
				Actually, I'm still a bit confused... Can I read the docs?
			</div>
		</a>

		<a href='/create'>
			<div class="btn btn-primary" style='float:right;margin:0 0 0 20px;' role="button">
				I get it! Onto my own template...
			</div>
		</a>
		<div class="btn btn-success pull-left" style='float:right;' id='sendFirstEmail' role="button">
			Send Email to Myself
		</div>

		<div style='float:left;margin-left:20px;display:none;' id='firstEmailSending'>
			<img src='/images/ring.gif' width='30px' alt='Loading'>
		</div>
		<div style='float:left;margin-left:20px;display:none;' id='firstEmailSent'>
			<h4>Email sent!</h4>
		</div>
		<div class='clear'></div>
	</div>

@endsection