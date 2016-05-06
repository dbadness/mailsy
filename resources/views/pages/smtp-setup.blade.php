@extends('layouts.master')

@section('content')

	<!-- Success notification -->
	<div class="alert alert-success" role="alert">Successfully signed up and logged in as {!! $user->email !!}.</div>

	<!-- Explain what Mailsy does and why it needs SMTP setup -->
	<div class="page-header">
	  	<h1>Let's Set Up Mailsy for You</h1>
	  	<p class='lead'>
	  		Mailsy helps people reach out to prospective customers in a fast, flexible, and geniune way to increase sales opportunities. To get started, we'll need to add your company's email information to Mailsy so we can send emails on your behalf. Don't worry, we never keep your email password on file (ever) as email and internet security is very important to us.
	  	</p>
	</div>

	<div id='mailsyFlowDiagram'>
		<img src='/images/mailsy-flow.png'>
	</div>

	<!-- Offer the SMTP form (with helpful email to the IT department -->
	<h3>Your Email Settings</h3>
	<p class='lead'>We'll need to save your company's email settings to your profile so we can make it easy for you to send out hundreds of emails quickly and easily from your email address. The follow information is needed to do this so, if you need help from you IT department, feel free to use the email template we've created for you below or email <a href='mailto:support@mailsy.co'>Mailsy Support</a> and we'd be happy to help.</p>
	{!! Form::open(array('url' => '/smtp-save')) !!}
		{!! Form::token() !!}
		<input type='hidden' value='{!! $user->name !!}' name='user_name'>
		<div class="input-group">
		  <span class="input-group-addon" id="basic-addon1">SMTP Email Server Address</span>
		  @if(isset($_GET['server']))
		  	<input type="text" name='smtp_server' value="{!! $_GET['server'] !!}" class="form-control" placeholder="smtp.company.com" aria-describedby="basic-addon1">
		  @else
		  	<input type="text" name='smtp_server' class="form-control" placeholder="smtp.company.com" aria-describedby="basic-addon1">
		  @endif
		</div>
		<br>
		<div class="input-group">
		  <span class="input-group-addon" id="basic-addon1">Email Username</span>
		  @if(isset($_GET['uname']))
		  	<input type="text" name='smtp_uname' value="{!! $_GET['uname'] !!}" class="form-control" placeholder="you@company.com" aria-describedby="basic-addon1">
		  @else
		  	<input type="text" name='smtp_uname' class="form-control" placeholder="you@company.com" aria-describedby="basic-addon1">
		  @endif
		</div>
		<br>
		<div class="input-group">
		  <label for='smtp_port'>SMTP Port:</label>
		  <select name='smtp_port' id='smtpPortSelect'>
		  	<option>587</option>
		  	<option>465</option>
		  	<option>25</option>
		  </select>
		</div>
		<br>
		<div class="input-group">
		  <label for='smtp_protocol'>SMTP Protocol:</label>
		  <select name='smtp_protocol' id='smtpProtocolSelect'>
		  	<option value='tls'>TLS</option>
		  	<option value='ssl'>SSL</option>
		  	<option value='none'>Unencrypted</option>
		  </select>
		</div>
		<br>
		<!-- Save to the DB if the test email is succesful -->
		<button id='saveSmtpSettingsButton' class='btn btn-primary' role='button' style='display:none;'>Save Email Settings</button>

	{!! Form::close() !!}
	<!-- Send to tutorial upon completion -->

	<!-- Send a test email before saving to the DB -->
	<button id='testSmtpSettingsButton' class='btn btn-primary' role='button'>Send Test Email Yourself</button>
	<span id='testError' style='color:red;'></span>
	<span id='testSuccess' style='color:green;'></span>
	<div id='testErrorDetailsWrapper' style='display:none;'>
		<p class='lead'>It looks like some of the settings aren't correct. Here are the errors from the email server. <a id='itTeamTemplateDynamic'>(Use this template to send an email to your IT dept to ask for help)</a>
		<div id='testErrorDetails'></div>
	</div>

	<!-- Tester modal -->
	<!-- Modal -->
	<div id="smtpTesterModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Let's send an email to test your settings...</h4>
	      </div>
	      <div class="modal-body">
	      	<p>
	      		We'll always ask you for your email password when you send emails for security reasons. <br><br>Please enter your email password below to send a test email to yourself to make sure everything is set up correctly:
	      	</p>
	      	<div class="input-group">
	      	  <span class="input-group-addon" id="basic-addon1">Email Password</span>
	      	  <input type="password" name='smtp_password' class="form-control" aria-describedby="basic-addon1">
	      	</div>
	        <div id='smtpFeedback'></div>
	      </div>
	      <div class="modal-footer">
	      	<span id='smtpTestLoader' style='display:none;'><img src='/images/loader.gif'></span>
	        <button id='sendTestEmailButton' type="button" class="btn btn-primary">Send Test Email</button>
	      </div>
	    </div>

	  </div>
	</div>

@endsection