$(document).ready(function()
{

	// 
	if(typeof template !== 'undefined')
	{
		$('#signature').code(template);
	}

	// save the users settings
	$('#saveSettings').click(function()
	{
		$.ajax({
			method: 'POST',
			url: '/saveSettings',
			data: 
			{
				'_token' : $('input[name=_token').val(),
				'name': $('input[name=name').val(),
				'sf_address' : $('input[name=sf_address]').val(),
				'signature' : $('#signature').code(),
				'track_email' : $('#trackEmail').val(),
				'timezone' : $('#timezone').val()
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

	// find the stripe key in the doc
	var stripeKey = $('#stripeKey').val();

	// build stripe button
	var handler = StripeCheckout.configure({
		key: stripeKey,
		image: '/images/google-logo.png', // <-- make sure to put the logo here
		locale: 'auto',
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

	// downgrade a user if you're an admin
	$('.revokeAccessLink').click(function()
	{
		$.ajax({
			method: 'post',
			url: '/revokeAccess',
			data: {
				'_token' : $('input[name=_token').val(),
				'child_id' : $(this).attr('member')
			},
			error: function()
			{
				alert('Something went wrong. Please try again later or email hello@mailsy.co for help.');
			},
			success: function()
			{
				window.location = '/settings?message=downgradeSuccess';
			}
		});

	});

	/* ---------------------- Subscription modification handling ---------------------- */

	// show the save button if there are changes
	$('#subscriptionCount').on('change',function()
	{
		if($(this).val() != totalUsers)
		{
			$('#saveSubscriptionsButton').show();
		}
		else
		{
			$('#saveSubscriptionsButton').hide();
		}
	});

	// handle the changes if there are any
	$('#saveSubscriptionsButton').click(function()
	{
		if($('#subscriptionCount').val() == '0')
		{
			alert('You can\'t have a subscription quantity of zero. If you\'d like to cancel your Mailsy subscription all together, please close this window and do so below.');
		}
		else
		{
			// collect your variables
			var newSubs = parseInt($('#subscriptionCount').val());
			var totalUsers = parseInt($('#totalUsers').val());

			if(newSubs > totalUsers)
			{
				// find the new delta for the subscriptions
				var newAmount = (newSubs - totalUsers);

				// make sure they confirm but then use the card on file to update their subscription settings
				var validated = confirm((newAmount)+' licenses will be added to your subscription.');

				// set the direciton for the backend
				var direction = 'increase';

			}
			else
			{
				// find the new delta for the subscriptions
				var newAmount = ($('#totalUsers').val() - $('#subscriptionCount').val());

				// make sure they confirm but then use the card on file to update their subscription settings
				var validated = confirm((newAmount)+' licenses will be removed from your subscription.');

				// set the direciton for the backend
				var direction = 'decrease';

			}

			// with everything good to go, send the request
			if(validated)
			{
				$.ajax({
					url : '/updateSubscription/'+direction,
					type : 'post',
					data : {
						'_token' : $('input[name=_token').val(),
						'new_subs' : newSubs
					},
					beforeSend : function() {
						// show a loader
						$('#saveSubscriptionsButton').hide();
						$('#closeSubModalButton').hide();
						$('#subModalLoader').show();
					},
					success : function(response) {
						if(response == 'wrong_company')
						{
							window.location = '/settings?error=wrongCompany';
						}
						else if(response == 'need_more_free_licenses')
						{
							window.location = '/settings?error=notEnoughFreeLicenses';
						}
						else if(response == 'cant_be_zero')
						{
							window.location = '/settings?error=cantBeZero';
						}
						else if(response == 'success')
						{
							window.location = '/settings?message=subscriptionSuccessfullyUpdated';
						}
					},
					error : function(response) {
						console.log(response.responseText);
						alert('Something went wrong. Please email hello@mailsy.co for help.');
					}
				});
			}
		}
	});

	// Close Checkout on page navigation
	$(window).on('popstate', function() {
		handler.close();
	});
	
});