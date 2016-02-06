@extends('layouts.master')

@section('content')

	<div class='jumbotron'>
		<h1>Creating email templates</h1>
		<p>Mailsy works by creating email templates that have a message that you can use over and over again for a
		given purpose. Take a look at the one below and notice the pieces of information that start with '@@'. Those are the 
		pieces of information that you can change for each recipient.</p>
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