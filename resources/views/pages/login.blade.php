@extends('layouts.anon-master')

@section('PageJS')

@endsection

@section('content')

	<div class='signupOptions'>

		<div class="row">
			<div class="col-sm-4 col-md-6">
				<div class="thumbnail">
					<div class="caption">
						<h3>Log In with Google</h3>
						<p>
							<a href="/auth/0/0" class="btn btn-primary" role="button">Login</a> 
						</p>
					</div>
				</div>
			</div>

			<div class="col-sm-6 col-md-6">
				<div class="thumbnail">
					<div class="caption">
						<h3>Login with Email/Password:</h3>
						{!! Form::open(array('url' => '/login','id' => 'authForm'))!!}
							{!! Form::token() !!}
							<div class="input-group">
								<span>Email:</span>
								@if(isset($_GET['email']))
							  		<input type="text" name='email' class="form-control" value='{!! $_GET["email"] !!}'>
							  	@else
							  		<input type="text" name='email' class="form-control">
							  	@endif
							  	<br>
							  	<span>Password:</span>
							  	<input type="password" name='password' class="form-control">
							</div>
							<span id='badInfo' style='color:red;display:none;'>Please enter your email and password.</span>
							<br>
							<button id='signupButton' class="btn btn-primary" role="button">Log In</button>
						{!! Form::close() !!}
					</div>
				</div>
				<!-- Login error reporting -->
				@if(isset($_GET['email']))
			  		<span style='color:red;'>Incorrect email/password. Please try again.</span>
			  	@endif
			</div>
		</div>

@endsection