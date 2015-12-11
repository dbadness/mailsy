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
	*	Settings Page
	*
	*/

	// save the users settings
	$('#saveSettings').click(function()
	{
		$.ajax({
			method: 'POST',
			url: '/saveSettings',
			data: 
			{
				'_token' : $('input[name=_token').val(),
				'sf_address' : $('#sf_address').val(),
				'signature' : $('#signature').val()
			},
			error: function()
			{
				alert('Something went wrong.');
			},
			success: function(response) {
				if(response == 'success')
				{
					$('#settingsSaved').show();
				}
			}
		});
	});

	// build stripe button
	var handler = StripeCheckout.configure({
		key: 'pk_test_CIZBh7IaLuncqqScIchbbbuh',
		image: '', // <-- make sure to put the logo here
		locale: 'auto'
	});

	// update the users card
	$('#updateCardButton').click(function(){
		// Open Checkout with further options
		handler.open({
			name: 'Update Card Info',
			email: $('#userEmail').html(),
			allowRememberMe: false,
			token: function(token)
			{
				// send the token to the server and update the card info
				$.ajax({
					url: '/updateCard',
					method: 'post',
					data: {
						stripe_token: token.id,
						_token: $('input[name=_token]').val()
					},
					beforeSend: function()
					{
						$('#cardState').html('<img src="/images/loader.gif">');
					},
					success: function(response)
					{
						var data = $.parseJSON(response);
						// insert the new card info into the page
						$('#lastFour').html('Last four: '+data.last4);
						$('#cardExp').html('Exp: '+data.exp_month+'/'+data.exp_year);
						$('#cardState').html('<span style="color:green;">Card details updated!</span>');
					},
					error: function()
					{
						alert('Something went wrong. Please contact hello@mailsy.co to update your information');
					}
				});
			}
		});
	});
			

// Close Checkout on page navigation
	$(window).on('popstate', function() {
		handler.close();
	});

	
}); // end doc ready