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

			@elseif($_GET['message'] == 'subscriptionSuccessfullyUpdated')

				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					You've successfully updated your number of paid Mailsy licenses!
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
					You can't have a subscription quantity of zero. If you'd like to cancel your Mailsy subscription, please do so in settings.
				</div>
				
			@elseif($_GET['error'] == 'UserNotInTeam')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					The user you tried to remove from a team doesn't appear to be part of that team.
				</div>
				
			@elseif($_GET['error'] == 'AlreadyAdmin')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					That user is already a team leader!
				</div>
				
			@elseif($_GET['error'] == 'AlreadyOnTeam')

				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					That user is already on another team!
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

	@if($user->status == 'paying' && $user->team_admin)
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>My Team Management</strong>
				<br>
				<br>

			</div>
			<div class="panel-body">

			@if($user->has_users)

				<table style='width:100%;'>
					{!! Form::token() !!}
				    @foreach($children as $child)
				    	<tr>
				    		@if($child->id != $user->id)
					    		<td><p>{!! $child->email !!} <a member='{!! $child->id !!}' class='btn btn-danger pull-right'>Remove From Team</a></p></td>
					    	@else
					    		<td><p>{!! $child->email !!} <a member='{!! $child->id !!}' class='btn btn-danger disabled pull-right'>You're Team Admin!</a></p></td>

				    		@endif
				    	</tr>
				    @endforeach
				</table>

			@endif

			</div>
		</div>
	@endif

	@if($user->status == 'paying' && isset($user->stripe_id))

	<div class="alert alert-success alert-dismissible" role="alert" id='settingsSaved'>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		Settings saved.
	</div>

	{!! Form::token() !!}
	<div style='display:none;' id='userEmail'>{!! $user->email !!}</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>User Management</strong>
				<br>
				<br>
				@if($user->admin && isset($user->stripe_id))
			
					<p>You have <b>{!! $company->users_left !!}</b> licenses left out of the <b>{!! $company->total_users !!}</b> in your subscription. <span class='a' id='subscriptionModalButton' data-toggle="modal" data-target="#subscriptionModal">Add/Remove Licenses</span><br><br>Remember that you can invite people to join Mailsy at <a href='/join/{!! $company->domain !!}' target='_blank'>www.mailsy.co/team/{!! $company->domain !!}</a> to use your licenses!</p>

					<!-- Make a modal for subscription handling -->
					<!-- Modal -->
					<div id="subscriptionModal" class="modal fade" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
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

				@elseif($user->admin)

				You are being paid for by {{$company->domain}}! They've given you admin privileges.

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
				    		<td><p><a class="btn btn-primary" id='userModalButton' data-toggle="modal" data-target="#userModal{{$child->id}}">{!! $child->email !!}</a>
				    		@if($user->team_admin == 0)
					    		<a member='{!! $child->id !!}' class='revokeAccessLink'>Downgrade to Free Account</a>
					    	@else
					    		<a member='{!! $child->id !!}' class='btn btn-danger pull-right disabled'>You Cannot Downgrade Someone Leading a Team</a>
				    		@endif
				    		</p></td>
				    	</tr>
				    @endforeach
				</table>

				    @foreach($children as $child)
					<!-- Make a modal for team handling -->
					<!-- Modal -->
					<div id="userModal{{$child->id}}" class="modal fade" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">{{$child->email}} User Management</h4>
								</div>
								<div class="modal-body">
									@if($child->team_admin == null && $child->belongs_to_team == null)
										<a href="/makeTeam/{{$child->id}}" class="btn btn-primary">Make New Team Lead by This User</a>
										<br>
										<br>
										<h6>Teams</h6>
										<hr>
										@foreach($teams as $team)
											{{$team->name}}
											<a href="/addToTeam/{{$child->id}}/{{$team->id}}" class="btn btn-primary pull-right">Add To Team</a>
										@endforeach
									@elseif($child->team_admin == 1)
										<a href="/destroyTeam/{{$child->id}}" id="destroyTeam" class="btn btn-danger">Destroy Team Led by This Person</a>
									@else
										<h6>Team</h6>
										<a href="/removeFromTeam/{{$child->id}}" class="btn btn-danger">Remove From Team</a>
									@endif

								</div>
								<br>
								<br>
								<div class="modal-footer">
									<img id='subModalLoader' style='display:none;' src='/images/loader.gif'>
									<button type="button" class="btn btn-default" data-dismiss="modal" id='closeSubModalButton'>Close</button>
								</div>
							</div>
						</div>
					</div>
				    @endforeach

			@endif

			</div>
		</div>

	@endif

@endsection