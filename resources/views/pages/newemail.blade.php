@extends('layouts.master')

@section('content')

	<p>Make a new email to send out:</p>
	{!! Form::token() !!}
	<textarea id='template' style="resize:none;width:500px;height:100px;"></textarea>
	<br>
	<button id='addContacts'>Add Contacts</button>
	<br><br>
	<span id='loading' style='display:none;'>Loading...</span>
	<div id='results'></div>

@endsection