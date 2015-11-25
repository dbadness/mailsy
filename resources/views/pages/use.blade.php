@extends('layouts.master')

@section('content')

<div class="well well-lg">
	{!! $email->template !!}
</div>

@endsection