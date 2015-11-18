$(document).ready(function(){

	// return the list of input fields for this template
	$('#addContacts').click(function(){
		$.ajax({
			method: "POST",
			url: '/addContacts',
			data: 
			{
				'email' : $('#email').val(),
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
					$('.recipientRow').append('<div class=\'field\'><input class=\'fieldInput\' name='+v+'></div>');
				});
				$('.recipientRow').append('<div id=\'recipientRowClear\' class=\'clear\'></div>');
				$('#recipients').show();
				$('#fields').show();
				$('#addRecipient').show();
			}
		});
	});

	// add another row of recipients to the list
	$('#addRecipient').click(function()
	{
		var row = $('#recipients:first-child');
		$('#recipients').append(row);
	});
	
}); // end doc ready