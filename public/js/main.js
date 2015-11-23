$(document).ready(function(){

	// take the content from the template form the user and process them into the form to be submitted
	$('#emailTemplate').keyup(function()
	{
		$('#emailTemplateHolder').val($('#emailTemplate').val());
	});
	$('#updatePreviews').click(function()
	{
		$('#emailTemplateHolder').val($('#emailTemplate').val());
	});
	// for the edit page since it's already populated

	// return the list of input fields for this template
	$('#addContacts').click(function(){
		$.ajax({
			method: "POST",
			url: '/addContacts',
			data: 
			{
				'_content' : $('#subject').val()+' '+$('#emailTemplate').val(),
				'_token' : $('input[name=_token]').val()
			},
			error: function()
			{
				alert('Something went wrong.');
			},
			beforeSend: function() {
				$('#loading').show();
			},
			success: function(response) {
				$('#loading').hide();
				var data = $.parseJSON(response);
				var count = 0;
				$.each(data,function(k,v)
				{
					$('#headers').append('<div class=\'header\'>'+v+'</div>');
				});
				$('#headers').append('<div class=\'clear\'></div>');
				$.each(data,function(k,v)
				{
					$('.recipientRow').append('<div class=\'field\'><input class=\'fieldInput\' name='+v+'[]></div>');
				});
				$('.recipientRow').append('<div id=\'recipientRowClear\' class=\'clear\'></div>');
				$('#recipients').show();
				$('#fields').show();
				$('#addRecipient').show();
				$('#viewPreviews').show();
				// make a global variable to duplicate the rows later
				row = $('#recipients').html();
			}
		});
	});

	// add another row of recipients to the list
	$('#addRecipient').click(function()
	{
		$('#recipients').append(row);
	});

	// save the users settings
	$('#saveSettings').click(function()
	{
		$.ajax({
			method: "POST",
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
	
}); // end doc ready