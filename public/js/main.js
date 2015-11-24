$(document).ready(function(){

	/**
	*
	* Create/edit email pages
	*
	*/

	// for the edit page since it's already populated
	$('#updatePreviews').click(function()
	{
		$('#emailTemplateHolder').val($('#emailTemplate').code());
	});

	// return the list of input fields for this template
	$('#addContacts').click(function(){

		// take the markup from the email and put it into the hidden textarea
		$('#emailTemplateHolder').val($('#emailTemplate').code());

		$.ajax({
			method: 'POST',
			url: '/addContacts',
			data: 
			{
				'_content' : $('#subject').val()+' '+$('#emailTemplateHolder').val(),
				'_token' : $('input[name=_token]').val()
			},
			error: function()
			{
				alert('Something went wrong.');
			},
			beforeSend: function() {
				$('#addContacts').html('Loading.........');
			},
			success: function(response) {
				$('#addContacts').html('Add Contacts');
				var data = $.parseJSON(response);
				var count = 0;
				$.each(data,function(k,v)
				{
					$('#headers').append('<td class=\'field\'><b>'+v+'</b></td>');
				});
				$.each(data,function(k,v)
				{
					$('#recipient').append('<td class=\'field\'><input type="text" name="'+v+'[]" class="form-control"></td>');
				});
				$('#fields').show();
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

	// initialise the editor
	$('#emailTemplate').summernote(
	{
		height: 300, // set editor height
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