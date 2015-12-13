$(document).ready(function()
{

	/**
	* 
	*	Upgrade Page
	*	
	*/ 

	// build stripe button
	var handler = StripeCheckout.configure({
		key: 'pk_test_CIZBh7IaLuncqqScIchbbbuh',
		image: '', // <-- make sure to put the logo here
		locale: 'auto',
		token: function(token) {
		// Use the token to create the charge with a server-side script.
		// You can access the token ID with `token.id`
		}
	});

	$('#customButton').on('click', function(e) {

		// add up the users
		var userCount = 0;
		// is the user paying for themself?
		if($('input[name=myself]').is(':checked'))
		{	
			userCount++;
			var myself = true;
		}
		var newUsers = $('#otherUsers').serializeArray();
		$.each(newUsers, function(i,user)
		{
			if(user.value != '')
			{
				userCount++;
			}
		});
		// add up the users to get the final amount
		var totalAmount = 700*userCount;
		var customerEmail = $('input[name=myEmail]').val();
		var customerInfo;
		if(myself && (userCount > 1))
		{
			if(userCount == 2)
			{
				customerInfo = 'For you and '+(userCount-1)+' other';
			}
			else
			{
				customerInfo = 'For you and '+(userCount-1)+' others';
			}
		}
		if(!myself && (userCount > 0))
		{
			if(userCount == 1)
			{
				customerInfo = 'For '+userCount+' other';
			}
			else
			{
				customerInfo = 'For '+userCount+' others';
			}
		}

		// make sure there is at least a user to pay for
		if(userCount > 0)
		{
			// Open Checkout with further options
			handler.open({
				name: 'One Month of Mailsy',
				description: customerInfo,
				amount: totalAmount,
				email: customerEmail,
				allowRememberMe: false,
				token: function(token)
				{
					// this is the callback from the authorization
					$('#otherUsers').append('<input type="hidden" name="stripe_token" value="'+token.id+'">');
					$('#otherUsers').append('<input type="hidden" name="_token" value="'+$('input[name=_token]').val()+'">');
					if(myself)
					{
						$('#otherUsers').append('<input type="checkbox" name="myself" id="myselfCheckbox" checked="checked">');
					}
					$('#otherUsers').submit();
				}
			});
		}
		else
		{
			e.preventDefault();
		}
	});

	// Close Checkout on page navigation
	$(window).on('popstate', function() {
		handler.close();
	});

	// adding more users to the customers account (so they can pay for other people)
	// pay for oneself
	var clicked = 0;
	$('#myselfButton').click(function()
	{
		if(clicked == 0)
		{
			$(this).prepend('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ');
			$('input[name=myself').attr('checked','checked');
			clicked = 1;
		}else
		{
			$(this).html('Pay for Myself');
			$('input[name=myself').attr('checked',null);
			clicked = 0;
		}
		
	});

	// add more users
	$('#addUsers').click(function()
	{
		$(this).html('Add Another Person');
		$('#otherUsers').append('<div class="userInput input-group newUserField">'+
									'<span class="input-group-addon removeUser"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>'+
									'<input type="text" name="newusers[]" class="form-control" placeholder="email@example.com">'+
									'</div>');
	});

	// check the DOM for the .removeUser class adn remove them on click
	$(document).on('click', '.removeUser', function() 
	{
		$(this).closest('.userInput').remove();
	});

	// add users page form submission logic
	$('#addUsersButton').click(function()
	{
		var userCount = 0;
		var emails = $('#otherUsers').serializeArray();
		$.each(emails, function(i,email)
		{
			if(email.value != '')
			{
				userCount++;
			}
		});
		// add their own userCount if they're add themselves
		if($('#myselfCheckbox').is(':checked'))
		{
			userCount++;
			$('#otherUsers').append('<input type="checkbox" name="myself" checked="checked" style="display:none;">');
		}

		if(userCount > 0)
		{
			var proratedAmount = Number($('#proratedAmount').val());
			var totalAmount = proratedAmount * userCount;
			var lastFour = $('#lastFour').val();
			if(userCount == 1)
			{
				var descriptor = 'person';
			}
			else
			{
				var descriptor = 'people';
			}
			$('#confirmPopup').html('This will add '+userCount+' '+descriptor+' to your Mailsy subscription and your card ending in '+lastFour+' will be charged $'+totalAmount+' immediately.<br><br>');
			$('#confirmPopup').append('<button class="btn btn-primary" role="button" id="submitAdditionForm">Sounds good, let\'s do it.</button>');
			$('#confirm').show();
		}

	});

	// after the user confirm the information in the popup...
	$(document).on('click', '#submitAdditionForm', function()
	{
		// append the token and submit the form
		$('#otherUsers').append('<input type="hidden" name="_token" value="'+$('input[name=_token]').val()+'">');
		$('#otherUsers').submit();
	});	
});