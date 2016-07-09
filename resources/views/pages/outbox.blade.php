@extends('layouts.master')

@section('content')

<?php
	function is_serialized($data)
	{
		return (@unserialize($data) !== false);
	}
?>

<div class="list-group">

	@if(count($messages) == 0)
		{{$messages}}
		<h2>No messages sent!</h2>
	@endif

	@foreach($messages as $message)
		<?php
			if(is_serialized($message->recipient))
			{
				$riter = unserialize($message->recipient);
				$citer = unserialize($message->cc);
				$biter = unserialize($message->bcc);

				$rout = '';
				$cout = '';
				$bout = '';

				for($i = 0; $i < count($riter); $i++){
					$rout = $rout.$riter[$i].', ';
				}
				for($i = 0; $i < count($citer); $i++){
					$cout = $cout.$citer[$i].', ';
				}
				for($i = 0; $i < count($biter); $i++){
					$bout = $bout.$biter[$i].', ';
				}

			} else{
				$rout = $message->recipient.',';
			}
		?>

		@if($message->read_at)
			<a href="#" class="list-group-item list-group-item-success" data-toggle="modal" data-target="#messageModal{{$message->id}}">{{$message->subject}} to {{$rout}} <span class="pull-right">Opened at <span class="unixToConvert">{{$message->read_at}}</span></span></a>
		@elseif($message->sent_at)
			<a href="#" class="list-group-item list-group-item-warning" data-toggle="modal" data-target="#messageModal{{$message->id}}">{{$message->subject}} to {{$rout}} <span class="pull-right">Sent at <span class="unixToConvert">{{$message->sent_at}}</span></span></a>
		@else
		<!-- put errors here -->
		@endif

		<!-- Modal -->
		<div id="messageModal{{$message->id}}" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Message to {!! $rout !!}</h4>
							@if($message->cc)
								<div class="modal-title">CC to {!! $cout !!}</div>
							@endif
							@if($message->bcc)
								<div class="modal-title">BCC to {!! $bout !!}</div>
							@endif
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
</div>

<span class="pull-right">{!! $messages->render() !!}</span>

@endsection
