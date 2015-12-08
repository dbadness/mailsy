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
		var customerInfo = 'For you';
		console.log(customerInfo);
		if(myself && (userCount > 1))
		{
			if(userCount == 2)
			{
				customerInfo += ' and '+(userCount-1)+' other';
			}
			else
			{
				customerInfo += ' and '+(userCount-1)+' others';
			}
		}
		if(!myself && (userCount > 0))
		{
			if(userCount == 1)
			{
				customerInfo += ' and '+userCount+' other';
			}
			else
			{
				customerInfo += ' and '+userCount+' others';
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
				allowRememberMe: false
			});
			e.preventDefault();
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
	$('#addUsers').click(function()
	{
		$('#addUser').show();
		$('#otherUsers').html('<div class="userInput input-group">'+
									'<span class="input-group-addon removeUser"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>'+
									'<input type="text" name="newusers[]" class="form-control" placeholder="email@example.com">'+
									'</div>');
	});
	$('#addUser').click(function(){
		$('#otherUsers').append('<div class="userInput input-group">'+
									'<span class="input-group-addon removeUser"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>'+
									'<input type="text" name="newusers[]" class="form-control" placeholder="email@example.com">'+
									'</div>');
	});

	// check the DOM for the .removeUser class adn remove them on click
	$(document).on('click', '.removeUser', function() 
	{
		$(this).closest('.userInput').remove();
	});	
	
});