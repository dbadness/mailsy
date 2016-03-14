@extends('layouts.master')

@section('content')

	<div class="jumbotron">
		<h3>Are you sure you want to cancel your membership?</h3>

		<p>You will also be able to use the paid version of Mailsy until {!! date('n/d/Y',$end_date) !!} after which you'll be downgraded to a free account (10 emails per day).</p>

		@if($user->has_users)
			<p>
			Since you're paying for other people, they'll also be allowed to use the paid version of Mailsy until {!! date('M-d-Y',$user->endDate) !!} after which they'll be downgraded to a free user account (10 emails per day).
		@endif
		<p>
			<a class="btn btn-danger cancelButton" id='masterCancel' style='float:right;' role="button">Cancel Membership</a>
			<a class="btn btn-primary" style='float:right;margin:0 20px 0 0;' href='/settings' role="button">Back to Settings</a>
		</p>
		<div class="clear"></div>
	</div>

@endsection