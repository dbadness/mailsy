@extends('layouts.anon-master')

@section('PageJS')

@endsection

@section('content')

	@if($_GET)
		@if(isset($_GET['error']))
			@if($_GET['error'] == 'accountExists')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've already signed up! Log in below.
				</div>

			@elseif($_GET['error'] == 'accountDNE')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					Account not found! Sign up below!
				</div>

			@endif
		@endif
	@endif

	<div class="page-header">
		<h1>Welcome to Mailsy!
		<br>
		<small>(You're about to become an email magician.)</h1>
	</div>

<!-- 	<p class='lead'>
		Mailsy enables you to engage with prospective and existing customers more effectively. You can sign up for Mailsy one of two ways: either with your Google account if you use Google Apps for Work or with an email and password if you don't. Don't worry, if you use a company we'll walk you through what you'll need to get Mailsy up and running.
	</p> -->

	<div class='signupOptions'>

		<div class="row">
			<div>
				<div class="thumbnail">
					<div class="caption">
						<h3>Use Mailsy with Google Account</h3>
						<p>Select this option if you use Google Apps for Work in your company.</p>
						<p>
							<a href="/auth/1/0" class="btn btn-primary" role="button">Sign Up</a> 
						</p>
					</div>
				</div>
			</div>

			<div>
				<div class="thumbnail">
					<div class="caption">
						<h3>Use Mailsy with Company Email</h3>
						<p>Select this option if you use a company email system. We'll help you set everything up in the next step.</p>
						<div id='signupWrapper'>
							@if($company)
								{!! Form::open(array('url' => '/signup/license', 'id' => 'authForm'))!!}
							@else
								{!! Form::open(array('url' => '/signup', 'id' => 'authForm'))!!}
							@endif
								{!! Form::token() !!}
								<div class="input-group">
									<span>Full Name:</span>
								  	<input type="text" name='name' class="form-control">
								  	<br>
									<span>Your Email:</span>
								  	<input type="text" name='email' class="form-control">
								  	<br>
								  	<span>A Password for Mailsy:</span>
								  	<input type="password" name='password' class="form-control">
								</div>
								<span id='badInfo' style='color:red;display:none;'>Please enter your name, email, and a password.</span>
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