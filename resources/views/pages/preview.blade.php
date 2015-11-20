@extends('layouts.master')

@section('content')

	<a href='/edit/{!! base64_encode($email->id) !!}'><button>Make Aome Edits</button></a>

@endsection