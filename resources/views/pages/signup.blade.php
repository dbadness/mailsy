@extends('layouts.anon-master')

@section('PageJS')

@endsection

@section('content')

	<div class="page-header">
		<h1>Welcome to Mailsy! <small>(You're about to become an email magician.)</h1>
	</div>
	<p class='lead'>
		Mailsy enables you to engage with prospective and existing customers more effectively. You can sign up for Mailsy one of two ways: either with your Google account if you use Google Apps for Work or with an email and password if you don't. Don't worry, if you don't use Google Apps for Work we'll walk you through what you'll need to set Mailsy up for sending emails.
	</p>

	<div class='signupOptions'>

		<div class="row">
			<div class="col-sm-4 col-md-6">
				<div class="thumbnail">
					<img src="..." alt="...">
					<div class="caption">
						<h3>Use Mailsy with Google Account</h3>
						<p>Select this option if you use Google Apps for Work in your company.</p>
						<p>
							<a href="/auth" class="btn btn-primary" role="button">Sign Up</a> 
						</p>
					</div>
				</div>
			</div>

			<div class="col-sm-6 col-md-6">
				<div class="thumbnail">
					<img src="..." alt="...">
					<div class="caption">
						<h3>Use Mailsy with Company Email</h3>
						<p>Select this option if you use a company email system. We'll help you set everything up in the next step.</p>
						<div id='signupWrapper'>
							{!! Form::open(array('url' => '/signup'))!!}
								{!! Form::token() !!}
								<div class="input-group">
									<span>Your Email:</span>
								  	<input type="text" name='email' class="form-control" aria-describedby="basic-addon1">
								  	<br>
								  	<br>
								  	<span>A Password for Mailsy:</span>
								  	<input type="password" name='password' class="form-control" aria-describedby="basic-addon1">
								</div>
						</div>
						<p>
							<button id='signupButton' class="btn btn-primary" role="button">Sign Up</button>
						</p>
								{!! Form::close() !!}
					</div>
				</div>
			</div>

		</div>

	</div>

@endsection