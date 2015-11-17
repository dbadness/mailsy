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
			}
		});
	});
	
}); // end doc ready