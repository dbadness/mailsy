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
				'track_email' : $('#trackEmail').val()
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

	var stripeKey = $('#stripeKey').val();

	// build stripe button
	var handler = StripeCheckout.configure({
		key: stripeKey,
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

	// Close Checkout on page navigation
	$(window).on('popstate', function() {
		handler.close();
	});
	
});