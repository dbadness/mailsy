@extends('layouts.master')

@section('content')

	<div class='jumbotron'>
		<h1>Welcome to Mailsy!</h1>
		<p>Mailsy allows you to send dozens of personalized emails to customers, prospects, colleagues, or whoever you decide quickly and easily.</p>
		<ul>
			<li><strong>Mailsy saves time</strong> - Mailsy's templating technology allows you build a message that you can use over and over again.</li>
			<li><strong>Mailsy is personal</strong> - Assign pieces of information in the template that you want to individualize for each recipient.</li>
			<li><strong>Mailsy is authentic</strong> - Mailsy emails come right from your email address, not from a marketing company. They can even be seen in your sent folder.</li>
			<li><strong>Mailsy is integrated</strong> - In the <a href='/settings'>settings page</a> you can add a signature and even a BCC email so your emails are tracking in your CRM system.</li>
		</ul>
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