@extends('layouts.master')

@section('content')

<div class="list-group">

	@if(count($messages) == 0)
		{{$messages}}
		<h2>No messages sent!</h2>
	@endif

	@foreach($messages as $message)
		@if($message->read_at)
			<a href="#" class="list-group-item list-group-item-success" data-toggle="modal" data-target="#messageModal{{$message->id}}">Email to {{$message->recipient}} <span class="pull-right">Opened at <span class="unixToConvert">{{$message->read_at}}</span></span></a>
		@elseif($message->sent_at)
			<a href="#" class="list-group-item list-group-item-warning" data-toggle="modal" data-target="#messageModal{{$message->id}}">Email to {{$message->recipient}} <span class="pull-right">Sent at <span class="unixToConvert">{{$message->sent_at}}</span></span></a>
		@else
			<a href="mailto:support@mailsy.co" class="list-group-item list-group-item-danger">Email to {{$message->recipient}} <span class="pull-right">Error</span></a>
		@endif

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
							<h6>{{ $message->subject }}</h6>
							<hr>
							{{ $message->message }}
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
</div>

<span class="pull-right">{!! $messages->render() !!}</span>

@endsection
