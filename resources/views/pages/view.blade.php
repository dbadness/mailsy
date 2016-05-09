@extends('layouts.master')

@section('content')
	<div class="page-header">
		<h1>{!! $email->name !!}</h1>
		<a href='/copy/{!! base64_encode($email->id) !!}'>Copy Template</a>
		<a href='/archives' class="pull-right">Back to Archives</a>
	</div>
	<br>
	<div class="input-group">
		<span class="input-group-addon" id="basic-addon4">Subject</span>
		<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4" disabled value='{{ $email->subject }}'>
	</div>
	<br>
	<div class="well">
		{!! $email->template !!}
	</div>

@endsection
