@extends('layouts.master')

@section('PageJS', '<script src="/js/messageAjax.js"></script>')

@section('content')

	<?php date_default_timezone_set('EST'); ?>

	@if($_GET)
		@if($_GET['message'] == 'success')
			<p style='color:green;'>Emails successfully sent.</p>
		@endif
	@endif
	<div class="page-header">
		<h1>View Messages <small>{!! $email->name !!}</small></h1>
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
				<td class='emailListRight'><b>Date Sent</b></td>
				<td class='emailListRight'><b>Status</b></td>
			</tr>
			@if($messages != '[]')
				@foreach($messages as $message)
					<input name='message_id' value='{!! $message->id !!}' type='hidden'/>
					<tr>
						<td>{!! $message->recipient !!}</td>
						<td class='emailListRight'><span class='viewMessage' messageId='{!! $message->id !!}'>View Message</span></td>
						<td class='emailListRight'>{!! date('D, M d, Y g:ia', $message->sent_at) !!} EST</td>
						<td class='emailListRight'><span id='status{!! $message->id !!}'></span></td>
					</tr>
					<tr>
						<td colspan='4' class='messageView' id='message{!! $message->id !!}'><br>{!! $message->message !!}<span class='close'>Close</span></td>
					</tr>
				@endforeach
			@endif
		</table>
	</div>
@endsection