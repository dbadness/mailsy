$(document).ready(function()
{
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
	
});