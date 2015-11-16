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
				var data = $.parseJSON(response);
				var count = 0;
				$.each(data, function(){
					$('#fields').append('<label for="'+this.fieldName+count+'">'+this.fieldLabel+'</label><input name="'+this.fieldName+count+'">');
					count++;
				});
				$('#loading').hide();
			}
		});
	});
	
}); // end doc ready