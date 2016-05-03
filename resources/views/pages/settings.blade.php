@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src="/js/settings.js"></script>

@endsection

@section('content')

	<!-- set the stripe token -->
	<input type='hidden' id='stripeKey' value="{!! env('STRIPE_P_TOKEN') !!}">

	<script>
		// fill in the #emailTemplate
		var template = '{!! addslashes($user->signature) !!}';
	</script>

	@if($_GET)
		@if(isset($_GET['message']))
			@if($_GET['message'] == 'upgradeSuccess')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've successfully upgraded to a paid membership!
				</div>

			@elseif($_GET['message'] == 'teamCreated')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've successfully created your team! You can send people to www.mailsy.co/join/{!! $company->domain !!} to have them signup for their paid versions of Mailsy.
				</div>

			@elseif($_GET['message'] == 'downgradeSuccess')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've successfully downgraded an account to a free account and one license has been added back to your subscription for future use.
				</div>

			@elseif($_GET['message'] == 'subscriptionSuccessfullyUpdated')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've successfully updated your number of paid Mailsy licenses!
				</div>

			@elseif($_GET['message'] == 'subscriptionCancelled')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've successfully cancelled your subscription.
				</div>

			@endif
		@endif

		@if(isset($_GET['error']))
			@if($_GET['error'] == 'wrongCompany')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've tried to access a company that you don't belong to.
				</div>

			@elseif($_GET['error'] == 'noLicenses')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					Your company is out of paid Mailsy licenses. Please email {!! $company->email !!} to request more.
				</div>

			@elseif($_GET['error'] == 'notEnoughFreeLicenses')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You can only remove licenses from your subscription that aren't being used. Please downgrade more users to free up more licenses so you can remove them from your subscription.
				</div>

			@elseif($_GET['error'] == 'cantBeZero')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You can't have a subscription quantity of zero. If you'd like to cancel your Mailsy subscription, please do so below.
				</div>
				
			@endif
		@endif
	@endif

	@if(!$user->paid && $company)

		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<p>You belong to a team that is already paying for Mailsy... Click the button below to use one of {!! $company->company_name !!}'s Mailsy licenses. If you have questions about this, please email {!! $company->email !!}.</p>
			<br>
			<form method='post' action='/useLicense'>
				<input type='hidden' name='company_id' value='{!! $company->id !!}'>
				{!! Form::token() !!}
				<button class='btn btn-success' role='button' id='addToSubButton'>Join the Team</button>
			</form>
		</div>

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
			<p>Email Tracking:</p>
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
			<p>Name:</p>
			<div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Name</span>
			  	<input type="text" name='name' class="form-control" aria-describedby="basic-addon1" value='{!! $user->name !!}'>
			</div>
			<br>
			<p>Timezone:</p>

			<select id='timezone' name='timezone' style='width:200px;'>
				<option value='America/New_York' <?php if($user->timezone == 'America/New_York'){echo 'selected';}?>>Eastern</option>
				<option value='America/Chicago' <?php if($user->timezone == 'America/Chicago'){echo 'selected';}?>>Central</option>
				<option value='America/Denver' <?php if($user->timezone == 'America/Denver'){echo 'selected';}?>>Mountain</option>
				<option value='America/Phoenix' <?php if($user->timezone == 'America/Phoenix'){echo 'selected';}?>>Mountain no DST</option>
				<option value='America/Los_Angeles' <?php if($user->timezone == 'America/Los_Angeles'){echo 'selected';}?>>Pacific</option>
				<option value='America/Anchorage' <?php if($user->timezone == 'America/Anchorage'){echo 'selected';}?>>Alaska</option>
				<option value='America/Adak' <?php if($user->timezone == 'America/Adak'){echo 'selected';}?>>Hawaii</option>
				<option value='Pacific/Honolulu' <?php if($user->timezone == 'Pacific/Honolulu'){echo 'selected';}?>>Hawaii no DST</option>
				<option value='Europe/London' <?php if($user->timezone == 'Europe/London'){echo 'selected';}?>>London</option>
				<option value='Europe/Berlin' <?php if($user->timezone == 'Europe/Berlin'){echo 'selected';}?>>Berlin</option>
			</select>
			<br>
			<br>
			<br>
			<p>Signature to be appended to the end of your emails:</p>
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


	@if($user->status == 'paying' && isset($user->stripe_id))

<!-- 		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>User Management</strong>
				<br>
				<br>
				@if($user->admin)
			
					<p>You have <b>{!! $company->users_left !!}</b> licenses left out of the <b>{!! $company->total_users !!}</b> in your subscription. <span class='a' id='subscriptionModalButton' data-toggle="modal" data-target="#subscriptionModal">Add/Remove Licenses</span><br><br>Remember that you can invite people to join Mailsy at <a href='/team/{!! $company->domain !!}' target='_blank'>www.mailsy.co/team/{!! $company->domain !!}</a> to use your licenses!</p>
 -->
					<!-- Make a modal for subscription handling -->
					<!-- Modal -->
<!-- 					<div id="subscriptionModal" class="modal fade" role="dialog">
						<div class="modal-dialog">
 -->							<!-- Modal content-->
	<!-- 						<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">Manage Subscriptions</h4>
								</div>
								<div class="modal-body">
									<p>Your subscriptions: <input type='number' id='subscriptionCount' min='0' style='width:60px;' value='{!! $company->total_users !!}'></p>
									<p><b>Note: You can only decrease your subscriptions if you have unused licenses (you have {!! $company->users_left !!} unused licenses). If you don't have any unused licenses, you can downgrade users on the Settings page and then come back here to reduce your licenses.</b></p>
									<input type='hidden' id='totalUsers' value='{!! $company->total_users !!}'>
									<input type='hidden' id='usersLeft' value='{!! $company->users_left !!}'>
								</div>
								<div class="modal-footer">
									<img id='subModalLoader' style='display:none;' src='/images/loader.gif'>
									<button type="button" class="btn btn-default" data-dismiss="modal" id='closeSubModalButton'>Close</button>
									<button type="button" id='saveSubscriptionsButton' style='display:none;' class="btn btn-primary">Save</button>
								</div>
							</div>
						</div>
					</div>

				@else

					<p>You're not paying for any other people. Want to <a href='/upgrade/createTeam'>create a team</a> to add some?</p>

				@endif

			</div>
			<div class="panel-body">

			@if($user->has_users)

				<table style='width:100%;'>
					{!! Form::token() !!}
				    @foreach($children as $child)
				    	<tr>
				    		<td><p>{!! $child->email !!} <a member='{!! $child->id !!}' class='revokeAccessLink'>Downgrade to Free Account</a></p></td>
				    	</tr>
				    @endforeach
				</table>

			@endif

			</div>
		</div> -->


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
							<td><p>Membership Status: Active</p></td>
						@elseif($user->state == 'deliquent')
							<td><p>Membership Status: Deliquent</p></td>
						@endif
						<td>
							<a href='/membership/cancel' class='cancelLink'>Cancel Subscription</a>
							<div class='clear'></div>
						</td>
					</tr>
				</table>
			</div>
		</div>

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