@extends('layouts.master')

@section('content')

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