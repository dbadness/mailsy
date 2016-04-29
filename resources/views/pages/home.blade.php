@extends('layouts.master')

@section('PageJS')
	<script src='/js/home.js'></script>
@endsection

@section('content')
	<br>
	<br>

	@if($emails == '[]')
		<div class="jumbotron">
			<h2>Prospecting is lightspeed with Mailsy.</h2>
			<p>Mailsy allows you to send multiple, individualized emails in seconds - 
			create a template, fill in the pieces of relevent information, 
			and  hit send. Just once!</p>
			<p>Dive right in and <a href='/create'><strong>create your first template!</strong></a> or check out the <a href='/faq'><strong>quickstart guide</strong></a>.</p>
		</div>

		<div class="alert alert-info" role="alert">
			No emails to report yet...
			
			@if(!$user->paid)
				<a href='/create' class='alert-link'>Create a template</a> and send up to 10 emails per today on the free account.
			@else
				<a href='/create' class='alert-link'>Create a template</a> and send hundreds of emails per day since you have an upgraded account!
			@endif
		</div>

		<!-- show the archived emails if there are any -->
		@if($archived > 0)

			<a href='/archives'>Archived Templates</a>

		@endif
		
	@else

		<!-- show the archived emails if there are any -->
		@if($archived > 0)

			<a href='/archives'>Archived Templates</a>

		@endif

		<!-- show the reponse rates for the emails -->

		<div class="row" id='emails'>
			@foreach($emails as $email)

				<?php
					// get the total message count
					$messageCount = App\Message::where('email_id',$email->id)->whereNotNull('status')->whereNull('deleted_at')->count();
				?>

				<div class="col-sm-6 col-md-4">
					<input type='hidden' name='email' value='{!! $email->id !!}'>
					<div class="thumbnail">
						<div class="caption">
							<h3>{!! $email->name !!}</h3>

							<div class='messageInfoWrapper'>
								<div class='messageInfo'>
									Reply Rate: <span id='replyRateForEmail{!! $email->id !!}'></span>%
								</div>
								<div class='messageInfo' style='border-right:solid 1px gray;'>
									{!! $messageCount !!} Messages
								</div>
								<div class='clear'></div>
							</div>

							<div class='templateWrapper'>
								{!! $email->template !!}
							</div>

							<!-- action buttons -->
							<a class="btn btn-primary" href='/use/{!! base64_encode($email->id) !!}'>use</a>
							<a class="btn btn-info" href='/email/{!! base64_encode($email->id) !!}'>messages</a>
							<a class="btn btn-info" href='/edit/{!! base64_encode($email->id) !!}'>edit</a>
							<a class="btn btn-info" href='/copy/{!! base64_encode($email->id) !!}'>copy</a>
							<a class="btn btn-danger" href='/archive/{!! base64_encode($email->id) !!}'>archive</a>
							
							@if($user->can_see_secrets == 1)
								<!-- actions for the hub -->
								@if($user->paid)
									<span class="dropdown">
										<button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown">Hub
										<span class="caret"></span></button>
										<ul class="dropdown-menu">
											@if($email->shared == 0)
												<li class="active"><a href="/hubify/{!! base64_encode($email->id) !!}/0">Private</a></li>
											@else
												<li><a href="/hubify/{!! base64_encode($email->id) !!}/0">Private</a></li>
											@endif
											@if($user->paid && ($user->has_users == "yes" || $user->belongs_to != null))
												@if($email->shared == 1)
													<li class="active"><a href="/hubify/{!! base64_encode($email->id) !!}/1">Company Hub</a></li>
												@else
													<li><a href="/hubify/{!! base64_encode($email->id) !!}/1">Company Hub</a></li>
												@endif
											@endif
											@if($email->shared == 2)
												<li class="active"><a href="/hubify/{!! base64_encode($email->id) !!}/2">Public Hub</a></li>
											@else
												<li><a href="/hubify/{!! base64_encode($email->id) !!}/2">Public Hub</a></li>
											@endif
										</ul>
									</span>
								@endif
							@endif
							
						</div>
					</div>
				</div>
			@endforeach
		</div>

	@endif

@endsection