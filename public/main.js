$(document).ready(function(){

	// return the list of input fields for this template
	$('#addContacts').click(function(){
		$.ajax({
			method: 'post',
			url: 'http://newco.dev/addContacts',
			data: 
			{
				'template' : $('#template').val(),
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
				$('#results').html(response);
			}
		});
	});
	
}); // end doc ready