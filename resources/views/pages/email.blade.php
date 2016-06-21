@extends('layouts.master')

@section('PageJS', '<script src="/js/messageAjax.js"></script>')

@section('content')

	<?php date_default_timezone_set($user->timezone); ?>

	@if($_GET)
		@if($_GET['message'] == 'success')
			<p style='color:green;'>Emails successfully sent.</p>
		@endif
	@endif
	<div class="page-header">
		<h1>View Messages <small>{!! $email->name !!}</small></h1><a href='/use/{!! base64_encode($email->id) !!}'>Send More Emails</a>
	</div>
	<div class="well">
		{!! $email->template !!}
	</div>

	<strong>Update: <span id='progressText'>0%</span></strong>
	<div class="progress">
		<div class="progress-bar" style="width:0%;"></div>
	</div>

	<!-- Set up the message list -->
	<div class="panel panel-default">
		<!-- Table -->
		<table class="table">
			<tr>
				<td><b>Recipient</b></td>
				<td class='emailListRight'><b>Message</b></td>
				<td class='emailListRight'><b>Sent</b></td>
				<td class='emailListRight'><b>Status</b></td>
				<td class='emailListRight'><b>Read</b></td>
			</tr>
			@if($messages != '[]')
				@foreach($messages as $message)
					<input name='message_id' value='{!! $message->id !!}' type='hidden'/>
					<tr>
						<td>{!! $message->recipient !!}</td>
						<td class='emailListRight'><a class="btn btn-primary" id='userModalButton' data-toggle="modal" data-target="#messageModal{{$message->id}}">View Message</a></td>
						<td class='emailListRight'>{!! date('D, M d, Y g:ia', $message->sent_at) !!} EST</td>
						<td class='emailListRight'><span id='status{!! $message->id !!}'></span></td>
						<td class='emailListRight'>
							@if($message->read_at)
								{!! date('D, M d, Y g:ia', $message->read_at) !!} EST
							@else
								--
							@endif
						</td>
					</tr>
					<tr>
						<td colspan='4' class='messageView' id='message{!! $message->id !!}'><br>{!! $message->message !!}<span class='close'>Close</span></td>
					</tr>
				@endforeach
			@endif
		</table>
	</div>

			@if($messages != '[]')
				@foreach($messages as $message)
		<!-- Modal -->
		<div id="messageModal{{$message->id}}" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Message to {!! $message->recipient !!}</h4>
						</div>
						<div class="modal-body">
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon4">Subject</span>
								<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4" disabled value="{!! $message->subject !!}">
							</div>
							<hr>
							<span class="input-group-addon" id="basic-addon4">Body</span>
							<div class="well">
								{!! $message->message !!}
							</div>
							<hr>

							@if($message->files == 'yes')
								<div>
									Files Attached (see them here coming soon!)
								</div>
							@else
								<div>
									No Files Attached
								</div>
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


@endsection