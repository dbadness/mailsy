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

	$('#individualUpgradeButton').on('click', function(e) {

		e.preventDefault();

		// get the customer info
		var customerInfo = 'for '+$('#userName').val();
		var customerEmail = $('#userEmail').val();

		// Open Checkout with further options
		handler.open({
			name: 'Mailsy Subscription',
			description: customerInfo,
			amount: 1000,
			email: customerEmail,
			allowRememberMe: false,
			token: function(token)
			{
				// this is the callback from the authorization
				$('#upgradeForm').append('<input type="hidden" name="stripe_token" value="'+token.id+'">');
				$('#upgradeForm').submit();
			}
		});

	});

	// handle the errors and submission of the create team form
	$('#createTeamButton').click(function(e)
	{
		// prevent the form from loading automatically
		e.preventDefault();

		// find your variables
		var customerEmail = $('#userEmail').val();
		var company = $('input[name=company_name]').val();
		var users = $('input[name=user_count]').val();

		// validate...
		var reg = new RegExp('[0-9]');
		if(!reg.test(users))
		{
			alert('Please make sure the number of users contains only numbers.');
		}
		else if(company.length == 0)
		{
			alert('Please make sure to enter a company name.');
		}
		else
		{
			// Open Checkout with further options
			handler.open({
				name: 'Mailsy Subscription',
				description: 'for '+company,
				amount: 1000*users,
				email: customerEmail,
				allowRememberMe: false,
				token: function(token)
				{
					// this is the callback from the authorization
					$('#createTeamForm').append('<input type="hidden" name="stripe_token" value="'+token.id+'">');
					$('#createTeamForm').submit();
				}
			});
		}

		// with everything validated bring up the stripe form
	});

	// Close Checkout on page navigation
	$(window).on('popstate', function() {
		handler.close();
	});	
});