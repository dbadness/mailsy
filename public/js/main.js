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
				$('#addContacts').hide();
				$('#refreshFields').show();
				var data = $.parseJSON(response);
				var count = 0;
				// set up the headers
				$('#recipientList').html('<tr id=\'headers\'><td class=\'field\'><b>Email</b></td></tr><tr id=\'recipient\'><td class=\'field\'><input type="text" name=\'_email[]\' class="form-control"></td></tr>');
				$.each(data.fields,function(k,v)
				{
					$('#headers').append('<td class=\'field\'><b>'+v+'</b></td>');
				});
				$.each(data.fields,function(k,v)
				{
					$('#recipient').append('<td class=\'field\'><input type="text" name="'+v+'[]" class="form-control"></td>');
				});
				$('#fields').show();
				// make a global variable to duplicate the rows later
				row = $('#recipient').wrap('<p/>').parent().html();
				$('#recipient').unwrap();
				$('#saved').show();
				$('#fields').append('<input type="hidden" name="_email_id" value="'+data.email+'">');
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
				$('#recipientList').html('<tr id=\'headers\'><td class=\'field\'><b>Email</b></td></tr><tr id=\'recipient\'><td class=\'field\'><input type="text" name=\'_email[]\' class="form-control"></td></tr>');
				$.each(data.fields,function(k,v)
				{
					$('#headers').append('<td class=\'field\'><b>'+v+'</b></td>');
				});
				$.each(data.fields,function(k,v)
				{
					$('#recipient').append('<td class=\'field\'><input type="text" name="'+v+'[]" class="form-control"></td>');
				});
				// make a global variable to duplicate the rows later
				row = $('#recipient').wrap('<p/>').parent().html();
				$('#recipient').unwrap();
			}
		});
	});

	// add another row of recipients to the list
	$('#addRecipient').click(function()
	{
		$('#recipientList').append(row);
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


	/**
	* 
	*	Upgrade Page
	*	
	*/ 

	// send to stripe
	jQuery(function($) {
		$('#payment-form').submit(function(event) {
			var $form = $(this);
			// Disable the submit button to prevent repeated clicks
			$form.find('button').prop('disabled', true);
			Stripe.card.createToken($form, stripeResponseHandler);
			// Prevent the form from submitting with the default action
			return false;
		});
	});

	function stripeResponseHandler(status, response) {
		var $form = $('#payment-form');

		if (response.error) {
			// Show the errors on the form
			$form.find('.payment-errors').text(response.error.message);
			$form.find('button').prop('disabled', false);
			$('#results').html(response.error);
		} else {
			// response contains id and card, which contains additional card details
			var token = response.id;
			// Insert the token into the form so it gets submitted to the server
			$form.append($('<input type="hidden" name="stripeToken" />').val(token));
			// and submit
			$form.get(0).submit();
		}
	};

	$('#addUsers').click(function()
	{
		$('#addUser').show();
		$('#addUsersField').html('<div class="userInput"><span class="removeUser">X</span><input name="newusers[]" placeholder="email@example.com"></div><br>');
	});
	$('#addUser').click(function(){
		$('#addUsersField').append('<div class="userInput"><span class="removeUser">X</span><input name="newusers[]" placeholder="email@example.com"></div><br>');
	});
	// check the DOM for the .removeUser class adn remove them on click
	$(document).on('click', '.removeUser', function() 
	{
		$(this).closest('.userInput').remove();
	});	
}); // end doc ready