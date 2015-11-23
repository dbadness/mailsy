@extends('layouts.master')

@section('content')

	<p>Edit '{!! $email->name !!}':</p>
	{!! Form::token() !!}
	<form method='post' action='/updatePreviews'>
		<input type='hidden' name='_email_id' value='{!! $email->id !!}'>
		<input name='_subject' id='subject' value='{!! $email->subject !!}'><br><br>
		<textarea id='emailTemplate'>{!! $email->template !!}</textarea>
		<br>
		<div class='button' id='addRecipient'>Add Another Recipient</div>
		<br><br>
		<span id='loading'>Loading...</span>
		<div id='fields' style='display:block;'>
			@if($email->temp_recipients_list)
				<div id='headers'>
					<div class='field'>
						Email
					</div>
					<?php $recipientArray = json_decode($email->temp_recipients_list); ?>
					@foreach(json_decode($recipientArray[0]->_fields) as $k => $v)
						@foreach($v as $field => $value)
							<div class='field'>
								{!! $field !!}
							</div>	
						@endforeach
					@endforeach
					<div class='clear'></div>
				</div>
					<div id='recipients' style='display:block;'>
						@foreach($recipientArray as $recipient)
							<div class='recipientRow'>
								<div class='field'>
									<input name='_email[]' class='fieldInput' value='{!! $recipient->_email !!}'>
								</div>
								@foreach(json_decode($recipient->_fields) as $k => $v)
									@foreach($v as $field => $value)
										<div class='field'>
											<input name='{!! $field !!}[]' value='{!! $value !!}'>
										</div>	
									@endforeach
								@endforeach
								<div class='clear'></div>
							</div>
						@endforeach
					</div>
			@endif
		</div>
		<textarea name='_email_template' id='emailTemplateHolder'></textarea>
		{!! Form::token() !!}
		<br>
		<input id='updatePreviews' style='display:block;' type='submit' value='Save and View Previews'>
	</form>

@endsection