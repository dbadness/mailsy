@extends('layouts.master')

@section('PageJS')

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script src='/js/upgrade.js'></script>

@endsection

@section('content')

	@if($user->paid || ($user->status == 'paying'))
		<div class="jumbotron">
			<h3>You already have a paid account! (Thank you.)</h3>
			<p>If you you'd like to administrate people that you're paying for you can do so in the <a href='/settings'>Settings</a> page. If you'd like to upgrade yourself to the subscription you current have for others, you can do so <a href='/membership/add'>here.</a></p>
		</div>
	@else
		<div class="page-header">
		  	<h1>Professional-level prospecting awaits you!</h1>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4><strong>Reduce time prospecting while increasing the effectiveness of your cold emails with Mailsy.</strong></h4>
				<p>By signing up for a Mailsy membership, you'll be able to send an unlimited number of emails through the service per day. 
				You can either become an individual paid member or, if you're a leader on your team, you can sign your entire team up all at once making
				billing and membership administration very easy. Don't worry, you can always change these settings later!</p>
				<p>If you have any questions about billing, membership administration, or anything else, please visit the <a href='/faq'>FAQ page</a>
				or send an email to <a href="mailto:hello@mailsy.co">hello@mailsy.co</a> and you'll get a speedy response.</p> 
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-2 col-md-6">
						<div class="thumbnail">
							<div class="caption">
								<h3><i class='fa fa-user'></i> Individual Signup</h3>
								<p>If you're a person that wants to upgrade Mailsy for individual use, this is the pefect option for you. For $10 a month, you can send up to 2000 emails per day through Mailsy (the daily limit on your Google Account imposed by Google).</p>
								<br>
								<form method='post' action='/upgrade' id='upgradeForm'>
									<input type='hidden' id='userEmail' value='{!! $user->email !!}'>
									<input type='hidden' id='userName' value='{!! $user->name !!}'>
									{!! Form::token() !!}
									<p><button class="btn btn-success" role="button" id='individualUpgradeButton'>Upgrade</button></p>
								</form>
							</div>
						</div>
					</div>
					<div class="col-sm-6 col-md-6">
						<div class="thumbnail">
							<div class="caption">
								<h3><i class='fa fa-users'></i> Team Signup</h3>
								<p>If your a team leader, use this option to pick the number of users on your team and be billed in a single monthly payment to make your life easy as pie. With the Team Signup process, we'll create a simple url for you like "www.mailsy.co/myCompany" where your company can easily signup for the Mailsy licences that you purchased.</p>
								<br>
								<p><a href='/upgrade/createTeam'><button class="btn btn-success" role="button">Create a Team</button></a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif


@endsection