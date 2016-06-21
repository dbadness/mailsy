@extends('layouts.master')

@section('PageJS')
	<script src='/js/home.js'></script>
@endsection

@section('content')

	<!-- show the archived emails if there are any -->
	@if($archived > 0)

		<a href="{{ route('getArchive') }}">Archived Templates</a>
		<hr>

	@endif

	@if(count($emails) < 1)
		<hr>
		<div class="jumbotron">
			<h2>Prospecting is lightspeed with Mailsy.</h2>
			<p>Dive right in and <a href="{{ route('create') }}"><strong>create your first template!</strong></a>.</p>
		</div>

		<div class="alert alert-info" role="alert">
			You have no templates yet...
			
			@if(!$user->paid)
				<a href="{{ route('create') }}" class='alert-link'>Create a template</a> and send up to 10 emails per today on the free account.
			@else
				<a href="{{ route('create') }}" class='alert-link'>Create a template</a> and send hundreds of emails per day since you have an upgraded account!
			@endif
		</div>
		
	@else

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
							<h3>{!! str_limit($email->name, $limit = 15, $end = '...') !!}</h3>

							<div class='messageInfoWrapper'>
								<div class='messageInfo' style='border-left:solid 1px gray;'>
									Stats Coming Soon!
								</div>
								<div class='messageInfo'>
									{!! $messageCount !!} Messages
								</div>
								<div class='clear'></div>
							</div>

							<div style="height:100px;">
								{!! str_limit($email->template, $limit = 325, $end = '...') !!}
							</div>

							<!-- action buttons -->
							<hr>
							<ul class="nav nav-pills nav-stacked">
								<li>
									<a class="btn btn-primary" href="{!! route('use', array( base64_encode($email->id) )) !!}" style="width: 100%;">use</a>
								</li>
								<li>
									<ul class="nav nav-pills" style="text-align: center;">
										<li>
											<a class="btn btn-info" href="{!! route('email', array( base64_encode($email->id) )) !!}">messages</a>
										</li>
										<li>
											<a class="btn btn-info" href="{!! route('edit', array( base64_encode($email->id) )) !!}">edit</a>
										</li>
										<li>
											<a class="btn btn-info" href="{!! route('copy', array( base64_encode($email->id) )) !!}">copy</a>
										</li>
									</ul>
								</li>
								<li>
									<a class="btn btn-danger" href="{!! route('archive', array( base64_encode($email->id) )) !!}" style="width: 100%">archive</a>
								</li>
							</ul>

							@if($user->can_see_secrets == 1)
								<!-- actions for the hub -->
								@if($user->paid)
									<span class="dropdown">
										<button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown">Hub
										<span class="caret"></span></button>
										<ul class="dropdown-menu">
											@if($email->shared == 0)
												<li class="active"><a href="{!! route('archive', array( base64_encode($email->id) )) !!}""/hubify/{!! base64_encode($email->id) !!}/0">Private</a></li>
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

	<span class="pull-right">{!! $emails->render() !!}</span>

	@endif

@endsection