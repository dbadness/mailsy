$(document).ready(function()
{

	// logic for sending the emails
	var messages = $('.messages').serializeArray();
	var length = messages.length;
	var increment = Number((100/length).toFixed(2));
	var emailId = $('input[name=email_id]').val();
	var total = 0;
	var count = 0;
	var minutes = Math.floor(( ( length * .5 ) / 60 ));
	var seconds = Math.ceil( ( length * .5 ) % 60 );

	$('#sendButton').click(function()
	{

		// open the modal
		$('.timerMinu').text(String(minutes));
		$('.timerSecu').text(String(seconds));
		$('#emailModal').modal('show');

		// go through each mesage and send that email
		$.each(messages, function(i,id)
		{
			setTimeout(function(){
				$.ajax({
					url: '/sendEmail/'+emailId+'/'+id.value,
					success: function(response){
						// increment the progress bar
						total += increment;
						$('.progress-bar').css('width',total.toFixed(0)+'%');
						$('#progressText').html(total.toFixed(0)+'% Complete');
						// update the view with the status
						$('#status'+id.value).html(response);

						// show the close button
						$('#closeEmailModal').show();
					},
					error: function(response){
						alert('Something went wrong. Please log out of Mailsy, log back in, and try again.');
					}
				});
			}, (i*500))

		});
	});

	// send the user to the email page when they close the emailSender Modal
	$('#closeEmailModalButton').click(function()
	{
		window.location = '/email/'+btoa(emailId);
	});

}); // end of doc ready