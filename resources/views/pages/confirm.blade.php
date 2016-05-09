@extends('layouts.master')

@section('PageJS')
	
	

@endsection

@section('content')

	<div class="jumbotron">
		<h3>Are you sure you want to cancel your membership?</h3>

		<p>You will also be able to use the paid version of Mailsy until {!! date('n/d/Y',$end_date) !!} after which you'll be downgraded to a free account (10 emails per day).</p>

		@if($user->has_users)
			<p>
			Since you're paying for other people, they'll also be allowed to use the paid version of Mailsy until {!! date('M-d-Y',$user->endDate) !!} after which they'll be downgraded to a free user account (10 emails per day).
		@endif
		<p>
			{!! Form::token() !!}
			<span id="cancelationLoader" style='display:none;'><img src='/images/loader.gif'></span>
			<button class="btn btn-danger" id='masterCancelButton' style='float:right;' role="button">Cancel Membership</button>
			<button class="btn btn-primary" style='float:right;margin:0 20px 0 0;' href='/settings' role="button">Back to Settings</button>
		</p>
		<div class="clear"></div>
	</div>

	<script>
		// logic for cancelling the membership on the 'confirm' page
		$('#masterCancelButton').on('click',function(){
			$.ajax({
				method : 'post',
				url : '/membership/cancel',
				data: {
					'_token' : $('input[name=_token]').val()
				},
				success : function(){
					window.location = '/settings?message=subscriptionCancelled';
				},
				beforeSend: function() {
					$('#cancelationLoader').show();
				},
				error : function(){
					alert('Something went wrong... please email hello@mailsy.co for help');
				}
			});
		});
	</script>

@endsection