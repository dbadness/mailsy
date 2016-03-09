
@extends('layouts.master')

@section('content')

	@if($_GET)
		@if($_GET['badEmails'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				At least one email is bad
			</div>
		@endif
		@if($_GET['missingColumns'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				At least one column is missing or mismatched
			</div>
		@endif
		@if($_GET['droppedRows'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Some rows have been dropped due to not having emails. Check to make sure they weren't important.
			</div>
		@endif
		@if($_GET['columnMismatch'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				At least one column is empty
			</div>
		@endif
		@if($_GET['invalidCSV'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Your CSV is invalid. Usually this means you don't have an email column. Sometimes because there's no headers.
			</div>
		@endif
		@if($_GET['empty'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				You didn't enter any fields!
			</div>
		@endif

	@endif
	<div class="page-header">
		<h1>{!! $email->name !!}</h1>
		<a href='/edit/{!! base64_encode($email->id) !!}'>Edit Template</a>
	</div>

	<form method='post' action='/makePreviews' id='makePreviews' enctype="multipart/form-data">
		{!! Form::token() !!}
		{!! Form::hidden('_email_template', $email->template) !!}
		{!! Form::hidden('_subject', $email->subject) !!}
		<input type='hidden' name='_email_id' value='{!! $email->id !!}'>
		<br>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4" disabled value='{!! $email->subject !!}'>
		</div>
		<br>
		<div class="well">
			{!! $email->template !!}
		</div>
		<div id='checkHolders'>
			<div class='checkHolder' id='sfHolder'>
				<p>Send to Salesforce: <input type='checkbox' name='_send_to_salesforce' @if($user->sf_address) checked="checked" @endif></p>
			</div>
			<div class='checkHolder' id='sigHolder'>
				<p>Attach Signature: <input type='checkbox' name='_signature' @if($user->signature) checked="checked" @endif></p>
			</div>
			@if(!$user->sf_address || !$user->signature)
				<div class='checkHolder'>
					<p style='font-size:80%;'>Head to <a href='/settings'>the settings page</a> to add your signature and Salesforce email address</p>
				</div>
			@endif
			<div class='clear'></div>
		</div>
		<br>

		<div id="uploadData">
			<table class="table" id="recipientList">
				<tr id='headers'>
					<td style='width:40px;'></td>
					<td class='field'>
						<b>Email</b>
					</td>
					@foreach(json_decode($email->fields) as $field)
						<td class='field'>
							<b>{!! $field !!}</b>
						</td>
					@endforeach
				</tr>
				<tr class='recipient'>
					<td class='removeRow'>
						<div style='height:5px;'></div>
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
					</td>
					<td class='field'>
						<input type="text" class="form-control" name='_email[]'>
					</td>
					@foreach(json_decode($email->fields) as $field)
						<td class='field'>
							<input type="text" class="form-control" name='{!! $field !!}[]'>
						</td>
					@endforeach
				</tr>
			</table>
			<div class="btn btn-info" id='addRecipient' role="button">
				<span class="glyphicon glyphicon-plus-sign"></span> Add Another Recipient
			</div>
			<br>
			<br>
			<button class="btn btn-primary" id='viewPreviews' role="button">
				View Email Previews
			</button>

			<span class="btn btn-primary" id="showCSV" role="button">
				Or Upload a CSV
			</span>
		</div>

		<div id="uploadCSV">
			<!-- Trigger the modal with a button -->
			<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Upload CSV List of Emails and Message Information</button>

			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">

				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							<p><b>Upload a CSV</b>
								<input type="file" name="csvFile" id="csvFileUpload" accept=".csv" value="" />
							</p>
							<button class="btn btn-primary" id='viewPreviews' role="button" style='float:right;'>
								View Email Previews
							</button>
							<div class='clear'></div>
						</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>

				</div>
			</div>

			<span class="btn btn-primary" id="showData" role="button">
				Or Enter Data Manually
			</span>
		</div>

	</form>

@endsection