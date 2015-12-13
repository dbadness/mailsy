$(document).ready(function(){

	/**
	*
	* Create/edit/use email pages
	*
	*/

	// initialise the editor
	$('#emailTemplate').summernote(
	{
		height: 300, // set editor height
	});
	// initialise the editor
	$('#signatureField').summernote(
	{
		height: 300, // set editor height
	});

	// fill in the email template
	// the variable is in the view
	if(typeof template !== 'undefined')
	{
		$('#emailTemplate').code(template);
	}

	// for the edit page since it's already populated
	$('#saveTemplate').click(function()
	{
		$('#emailTemplateHolder').val($('#emailTemplate').code());
	});

	// make sure that there are values in the inputs
	$('#makePreviews').submit(function()
	{
		var fields = $('#recipientList input').serializeArray();
		for(var k in fields)
		{
			if(typeof fields[k] !== 'function')
			{
				if(fields[k].value === '')
				{
					alert('Please make sure all your fields are filled in!');
					return false;
				}
			}
		}
	});
	

	// return the list of input fields for this template
	$('#addContacts').click(function(){

		// take the markup from the email and put it into the hidden textarea
		$('#emailTemplateHolder').val($('#emailTemplate').code());

		$.ajax({
			method: 'post',
			url: '/returnFields',
			data: 
			{
				'_email_template' : $('#emailTemplateHolder').val(),
				'_name' : $('input[name=_name]').val(),
				'_subject' : $('#subject').val(),
				'_token' : $('input[name=_token]').val(),
				'_email_id' : $('input[name=_email_id]').val()
			},
			error: function()
			{
				alert('Something went wrong.');
			},
			beforeSend: function() {
				$('#addContacts').html('Loading...');
			},
			success: function(response) {

				// error reporting
				if(response == 'no main content')
				{
					$('#noContent').show();
					$('#addContacts').html('Save Template and Add Contacts');
				}
				else
				{
					$('#addContacts').hide();
					$('#refreshFields').show();
					var data = $.parseJSON(response);
					var count = 0;
					// set up the headers
					$('#recipientList').html('<tr id=\'headers\'><td style=\'width:40px;\'></td><td class=\'field\'><b>Email</b></td></tr><tr class=\'recipient\'><td class=\'removeRow\'><div style=\'height:5px;\'></div><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></td><td class=\'field\'><input type="text" name=\'_email[]\' class="form-control"></td></tr>');
					$.each(data.fields,function(k,v)
					{
						$('#headers').append('<td class=\'field\'><b>'+v+'</b></td>');
					});
					$.each(data.fields,function(k,v)
					{
						$('.recipient').append('<td class=\'field\'><input type="text" name="'+v+'[]" class="form-control"></td>');
					});
					$('#fields').show();
					// make a global variable to duplicate the rows later
					row = $('#recipient').wrap('<p/>').parent().html();
					$('#recipient').unwrap();
					$('#saved').show();
					$('#fields').append('<input type="hidden" name="_email_id" value="'+data.email+'">');
				}
			}
		});
	});

	// refresh the fields when the user hits the button "again"
	$('#refreshFields').click(function()
	{
		// take the markup from the email and put it into the hidden textarea
		$('#emailTemplateHolder').val($('#emailTemplate').code());

		$.ajax({
			method: 'post',
			url: '/returnFields',
			data: 
			{
				'_email_template' : $('#emailTemplateHolder').val(),
				'_name' : $('input[name=_name]').val(),
				'_subject' : $('#subject').val(),
				'_token' : $('input[name=_token]').val(),
				'_email_id' : $('input[name=_email_id]').val()
			},
			error: function()
			{
				alert('Something went wrong.');
			},
			beforeSend: function() {
				$('#refreshFields').html('Loading...');
			},
			success: function(response) {
				var data = $.parseJSON(response);
				var count = 0;
				$('#saved').show();
				$('#refreshFields').html('Save Template and Refresh Fields');
				// refresh the fields div
				$('#recipientList').html('<tr id=\'headers\'><td style=\'width:40px;\'></td><td class=\'field\'><b>Email</b></td></tr><tr class=\'recipient\'><td class=\'removeRow\'><div style=\'height:5px;\'></div><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></td><td class=\'field\'><input type="text" name=\'_email[]\' class="form-control"></td></tr>');
				$.each(data.fields,function(k,v)
				{
					$('#headers').append('<td class=\'field\'><b>'+v+'</b></td>');
				});
				$.each(data.fields,function(k,v)
				{
					$('.recipient').append('<td class=\'field\'><input type="text" name="'+v+'[]" class="form-control"></td>');
				});
			}
		});
	});

	// add another row of recipients to the list
	$('#addRecipient').click(function()
	{
		// make a global variable to duplicate the rows later
		row = $('.recipient:first').wrap('<p/>').parent().html();
		$('.recipient:first').unwrap();
		$('#recipientList').append(row);
	});

	// remove row
	$(document).on('click', '.removeRow:not(:first)', function()
	{
		$(this).parent().remove();

	});
	
	/**
	*
	*	Membership update confirmation Page
	*
	*/

	// cancelling a membership of someone the user is paying for
	$('.cancelButton').click(function()
	{
		var id = $(this).attr('ref');

		if($(this).attr('id') == 'masterCancel')
		{
			$.ajax({
				url: '/membership/cancel/master',
				method: 'post',
				data: {
					_token: $('input[name=_token]').val(),
				},
				success: function(response)
				{
					alert(response);
				},
				error: function()
				{
					alert('Something went wrong... :(');
				}
			});
		}
		else
		{
			$.ajax({
				url: '/membership/cancel',
				method: 'post',
				data: {
					_token: $('input[name=_token]').val(),
					ref: $(this).attr('ref')
				},
				success: function(response)
				{
					alert(response);
				},
				error: function()
				{
					alert('Something went wrong... :(');
				}
			});
		}
	});

	
}); // end doc ready