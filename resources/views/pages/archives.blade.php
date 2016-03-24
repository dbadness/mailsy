@extends('layouts.master')

@section('content')
	<br>
	<br>

	<div class="panel panel-success">
		<!-- Table -->
		<table class="table">
			<tr>
				<td><b>Email Name</b></td>
				<td class='emailListRight'><b>Emails Sent</b></td>
			</tr>
			@if($emails != '[]')
				@foreach($emails as $email)
					<?php
						$messageCount = App\Message::where('email_id',$email->id)->whereNotNull('status')->whereNull('deleted_at')->count();
					?>
					<tr>
						<td>
							<span><strong>{!! $email->name !!}</strong></span>
							<span class="pull-right">
								<a class="btn btn-info" href='/email/{!! base64_encode($email->id) !!}'>messages</a>
								<a class="btn btn-info" href='/copy/{!! base64_encode($email->id) !!}'>copy</a>
								<a class='btn btn-success' href='/dearchive/{!! base64_encode($email->id) !!}'>restore</a>
							</span>
						</td>
						<td class='emailListRight'>{!! $messageCount !!}
						</td>
					</tr>
				@endforeach
			@endif
		</table>
	</div>

@endsection