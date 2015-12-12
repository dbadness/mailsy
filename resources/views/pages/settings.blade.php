@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src="/js/settings.js"></script>

@endsection

@section('content')

	@if($_GET)
		@if($_GET['message'])
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				You've successfully upgraded to a paid membership! You'll get a receipt in the mail. Well, ok, email.
			</div>
		@endif
	@endif

	<div class="alert alert-success alert-dismissible" role="alert" id='settingsSaved'>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		Settings saved.
	</div>

	{!! Form::token() !!}
	<div style='display:none;' id='userEmail'>{!! $user->email !!}</div>

	<div class='page-header'>
		<h1>Settings</h1>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Email Settings</strong></div>
		<div class="panel-body">
			<p>Name (to appear in the inbox of the recipient - <i>highly recommended</i>):</p>
			<div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Name</span>
			  	<input type="text" name='name' class="form-control" aria-describedby="basic-addon1">
			</div>
			<br>
			<p>Signature to be inserted at the end of your emails:</p>
			<div id="signatureField"></div>
			<textarea name='_email_template' id='emailTemplateHolder'></textarea>
			<p>Salesforce email address to log your emails in Salesforce (if you select that option when sending emails):</p>
			<div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Salesforce Email</span>
			  	<input type="text" name='sf_address' class="form-control" aria-describedby="basic-addon1">
			</div>
			<br>
			<button class='btn btn-primary' id='saveSettings'>Save Email Settings</button>
		</div>
	</div>

	@if($user->paid)
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Card Settings</strong></div>
			<div class="panel-body">
				<div class='cardLeft'>
					<button id='updateCardButton' class='btn btn-primary' role='button'>Update Card</button>
				</div>
				<div class='cardLeft' id='lastFour' style='padding:8px 0 0 0;'>
					Last four: {!! $user->lastFour !!}
				</div>
				<div class='cardLeft' id='cardExp' style='padding:8px 0 0 0;'>
					Exp: {!! $user->exp !!}
				</div>
				<div class='cardLeft' style='padding:8px 0 0 0;'>
					Next Payment Due: {!! date('M-d-Y',$user->nextDue) !!}
				</div>
				<div class='cardLeft' id='cardState' style='padding:8px 0 0 0;'>
					@if($user->state)
						<span display='color:red;'>There's a problem with your card. Please update it to continue using your paid membership.</span>
					@endif
				</div>
				<div class='clear'></div>
				<br>
				<table style='width:100%;'>
					<tr>
						@if(!$user->state)
							<td><h5>Membership Status: Active</h5></td>
						@elseif($user->state == 'deliquent')
							<td><h5>Membership Status: Deliquent</h5></td>
						@endif
						<td>
							<a href='/membership/confirm/me/master' class='cancelLink'>Cancel Membership</a>
							<div class='clear'></div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		@if($user->has_users)
			<div class="panel panel-default">
				<div class="panel-heading"><strong>User Management</strong></div>
				<div class="panel-body">
					<table style='width:100%;'>
					    @foreach($children as $child)
					    	<tr>
					    		<td><h5>{!! $child->email !!}</h5></td>
					    		<td>
					    			<a href='/membership/confirm/{!! base64_encode($child->id.rand(10000,99999)) !!}' class='cancelLink'>Cancel Membership</a>
					    			<div class='clear'></div>
					    		</td>
					    	</tr>
					    @endforeach
					</table>
				</div>
			</div>
		@endif
	@endif

@endsection