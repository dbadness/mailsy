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
							<form method='get' action='/dearchive/{!! $email->id !!}' enctype="multipart/form-data">
								<span><strong>{!! $email->name !!}</strong></span>
								<span class="pull-right">
									<a class="btn btn-info" href='/email/{!! base64_encode($email->id) !!}'>info</a>
									<a class="btn btn-info" href='/edit/{!! base64_encode($email->id) !!}'>copy</a>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<button class="btn btn-danger" id='dearchiveEmail'>dearchive</button>
								</span>
							</form>

						</td>
						<td class='emailListRight'>{!! $messageCount !!}
						</td>
					</tr>
				@endforeach
			@endif
		</table>
	</div>

	<a href="/home">Dashboard</a>

@endsection