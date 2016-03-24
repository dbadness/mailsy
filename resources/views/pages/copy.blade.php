@extends('layouts.master')

@section('content')

	<script>
		// fill in the #emailTemplate
		var template = '{!! addslashes($email->template) !!}';
	</script>

	<div class="page-header">
		<h1>Copy Template <small>{!! $email->name !!}</small></h1>
	</div>
	<form method='post' action='/copyTemplate' enctype="multipart/form-data">
		{!! Form::token() !!}
		<input type='hidden' name='_email_id' value='{!! $email->id !!}'>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon3">Template Name</span>
			<input type='text' name='_name' class="form-control" aria-describedby="basic-addon3" value='{!! $email->name !!}'>
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" name='_subject' id='subject' class="form-control" aria-describedby="basic-addon4" value="{!! $email->subject !!}">
		</div>
		<br>
		<div id="emailTemplate"></div>
			<button class="btn btn-primary" role="button" id='saveTemplate'>
				Save Template as Copy
			</button>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
	</form>

@endsection