@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src="/js/settings.js"></script>

@endsection

@section('content')

	<script>
		// fill in the #emailTemplate
		var template = '{!! addslashes($user->signature) !!}';
	</script>

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
		<div class='panel-body'>
			<p>Email Tracking</p>
			<div class="input-group">
				<span class="input-group-addon">
					<select id='trackEmail' name='track_email'>
						@if($user->track_email)
							<option value='yes' selected>Yes</option>
							<option value='no'>No</option>
						@else
							<option value='yes'>Yes</option>
							<option value='no' selected>No</option>
						@endif
					</select>
				</span>
				<input type="text" class="form-control" value='Send me an email when someone opens my emails.' disabled>
			</div>
			<br>
			<p>Name (to appear in the inbox of the recipient - <i>highly recommended</i>):</p>
			<div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Name</span>
			  	<input type="text" name='name' class="form-control" aria-describedby="basic-addon1" value='{!! $user->name !!}'>
			</div>
			<br>
			<p>Signature to be inserted at the end of your emails:</p>
			<div id="signature"></div>
			<textarea name='_email_template' id='emailTemplateHolder'></textarea>
			<p>Salesforce email address to log your emails in Salesforce (if you select that option when sending emails):</p>
			<div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Salesforce Email</span>
			  	<input type="text" name='sf_address' class="form-control" aria-describedby="basic-addon1" value='{!! $user->sf_address !!}'>
			</div>
			<br>
			<button class='btn btn-primary' id='saveSettings'>Save Email Settings</button>
		</div>
	</div>

	@if($user->status == 'paying')
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
							<a href='/membership/cancel' class='cancelLink'>Cancel Subscription</a>
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
					    			<a member='{!! $child->id !!}' class='revokeAccessLink'>Revoke Access</a>
					    			<div class='clear'></div>
					    		</td>
					    	</tr>
					    @endforeach
					    <tr>
					    	<td> </td>
					    	@if(!$user->paid)
					    		<td style='text-align:right;'>Want to <a href='/upgrade'>yourself</a> to your account?</td>
					    	@endif
					    </tr>
					</table>
					<p>You have {!! $customer_details->users_left !!} licenses left out of the {!! $customer_details->total_users !!} you paid for. Remember that you can invite them to join Mailsy at <a href='www.mailsy.co/{!! $customer_details->domain !!}' target='_blank'>www.mailsy.co/{!! $customer_details->domain !!}</a>!</p>
				</div>
			</div>
		@elseif(!$user->has_users && $user->admin)
			<div class="panel panel-default">
				<div class="panel-heading"><strong>User Management</strong></div>
				<div class="panel-body">
					<p>You have {!! $customer_details->users_left !!} licenses left out of the {!! $customer_details->total_users !!} you paid for. Remember that you can invite them to join Mailsy at <a href='www.mailsy.co/{!! $customer_details->domain !!}' target='_blank'>www.mailsy.co/{!! $customer_details->domain !!}</a>!</p>
				</div>
			</div>
		@else
			<div class="panel panel-default">
				<div class="panel-heading"><strong>User Management</strong></div>
				<div class="panel-body">
					<p>You're not paying for any other people. Want to <a href='/upgrade/createTeam'>create a team</a> to add some?</p>
				</div>
			</div>
		@endif
	@elseif(!$user->status && $user->paid)
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Card Settings</strong></div>
			<div class="panel-body">
				You don't have a card registered since someone is paying for you.
			</div>
		</div>
	@else
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Card Settings</strong></div>
			<div class="panel-body">
				You don't have a card registered since you're on a free account. You can <a href='/upgrade'>upgrade</a> to make your account a paid membership, pay for others (like your team) to give them paid memberships, or both!
				<br>
				<br>
				<a href='/upgrade'><button class='btn btn-success'>Upgrade my Account!</button></a>
			</div>
		</div>
	@endif

@endsection