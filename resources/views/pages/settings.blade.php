@extends('layouts.master')

@section('content')

	@if($_GET)
		@if($_GET['message'])
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				You've successfully upgraded to a paid membership! You'll get a receipt in the mail. Well, ok, email.
			</div>
		@endif
	@endif
		Salesforce Email Address:
		<br>
		<input style='width:500px;' id='sf_address' value='{!! $user->sf_address !!}'>
		<br><br>
		Signature:
		<br>
		<textarea id='signature' class='textarea'>{!! $user->signature !!}</textarea>
		<br><br>
		{!! Form::token() !!}
		<button id='saveSettings'>Save Settings</button>
		<br>
		<br>
		<span style='color:green;display:none' id='settingsSaved'>Settings saved.</span>

@endsection