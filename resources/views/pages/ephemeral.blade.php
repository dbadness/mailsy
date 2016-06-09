@extends('layouts.master')

@section('content')

	@if($_GET)
		@if($_GET['badEmails'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				At least one email is bad
			</div>
		@endif
		@if($_GET['noHeaders'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				CSV Error: You don't have headers
			</div>
		@endif
		@if($_GET['noEmailInHeaders'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				CSV Error: There is no email field in the headers (call it email).
			</div>
		@endif
		@if($_GET['headerFieldMissing'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				CSV Error: At least one of the fields is not in the headers
			</div>
		@endif
		@if($_GET['rowsNotExtant'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				CSV Error: You are missing data beneath the headers
			</div>
		@endif
		@if($_GET['incompleteColumns'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				CSV Error: One of your columns is shorter than is acceptable, probably indicating lost data or ill formatted row
			</div>
		@endif
		@if($_GET['blankData'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				CSV Error: At least one of your fields is blank
			</div>
		@endif
		@if($_GET['tooLarge'] == 'true')
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Your CSV has exceeded Mailsy's maximum limit of {!! env('MESSAGE_MAX') !!} rows.
			</div>
		@endif
	@endif

	<form method='post' action='/makePreviews' id='makePreviews' enctype="multipart/form-data">
		{!! Form::token() !!}

		<div class="input-group">
			<span class="input-group-addon" id="basic-addon4">Subject</span>
			<input type="text" id='subject' class="form-control" aria-describedby="basic-addon4">
		</div>
		<br>
		<div id="emailTemplate"></div>

		<div id='checkHolders'>
			<div class='checkHolder' id='sfHolder'>
				<p>Send to Salesforce: <input type='checkbox' name='_send_to_salesforce' @if($user->sf_address) checked="checked" @endif></p>
			</div>
			<div class='checkHolder' id='sigHolder'>
				<p>Attach Signature: <input type='checkbox' name='_signature' @if($user->signature) checked="checked" @endif></p>
			</div>
			@if(!$user->sf_address || !$user->signature)
				<div class='checkHolder'>
					<p>Head to <a href='/settings'>the settings page</a> to add your signature and CRM BCC email address</p>
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

				</tr>
				<tr class='recipient'>
					<td class='removeRow'>
						<div style='height:5px;'></div>
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
					</td>
					<td class='field'>
						<input type="text" class="form-control" name='_email[]'>
					</td>

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
			or
			<span class="btn btn-primary" id="showCSV" role="button">
				Upload a CSV
			</span>
			or
			<span class="btn btn-primary" id="showCSV" role="button">
				Upload a CSV
			</span>
		</div>

		<div id="uploadCSV">
			<!-- Trigger the modal with a button -->
			<span class="btn btn-primary" role="button">
				Generate Fields and Refresh Data
			</span>
			<span class="pull-right">
				Fields:
			</span>
			<br>
			<br>

			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Upload CSV List of Emails and Message Information</button>
			or

			<span class="btn btn-primary" id="showData" role="button">
				Enter Data Manually
			</span>

			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">

				<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<h4>Upload a CSV of recipient data</h4>
						</div>
						<div class="modal-body">
							<p>
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

		</div>

	</form>

@endsection
