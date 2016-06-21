@extends('layouts.master')

@section('content')

	@if(count($events) == 0)
		No new events since your last login! <a href="{{ route('sendone') }}">Send some more emails!</a>
	@else

	<div class="list-group">
	Events while you were gone:
		@foreach($events as $event)

			<a class="list-group-item list-group-item-success">{{$event->event_message}} <span class="pull-right">at <span class="unixToConvert">{{$event->timestamp}}</span></span></a>

		@endforeach
	</div>

	@endif

<span class="pull-right">{!! $events->render() !!}</span>

@endsection