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

	// the sending function
	function sendEmails(messageId,penguin = null)
	{

		console.log($('#fileToUpload')[0].files);

   	    var data = new FormData();
		$.each($('#fileToUpload')[0].files, function(key, file)
		{
	        data.append('_files[]', file);
		});

		$.ajax({
			url: '/sendEmail/'+emailId+'/'+messageId+'/'+penguin,
			method: 'post',
		    contentType: false,
		    processData: false,
        	headers: {
    	        'X-CSRF-TOKEN': $('input[name=_token]').val()
	        },
			data: data,
			success: function(response){

				// increment the progress bar
				total += increment;
				$('.preflight').addClass('hidden');
				$('.estimate').removeClass('hidden');
				$('.progress-bar').css('width',total.toFixed(0)+'%');
				$('#progressText').html(total.toFixed(0)+'% Complete');

				// show the close button
				$('#closeEmailModal').show();
			},
			error: function(){
				alert('Something went wrong. Please log out of Mailsy, log back in, and try again. If something\'s really broken, email us at support@lucolo.com and we\'d be happy to help.');
			}
		});
	}

	$('#sendButton').click(function()
	{
		// if they need to enter a password, let them
		if($('input[name=gmail_user]').val() == '0')
		{
			$('#passwordModal').modal('show');

			// handle the password submission and encryption
			$('#submitPasswordButton').click(function()
			{
				var penguin = $('input[name=penguin]').val();

				// validate the password field
				if(penguin ==  '')
				{
					$('#noPenguin').show();
				}
				else
				{
					$('#noPenguin').hide();
					penguin = window.btoa(penguin);

					// check to see if the password is correct
					$.ajax({
						url: '/smtp-auth-check/'+penguin,
						type: 'get',
						beforeSend: function()
						{
							$('#checkingAuth').show();
						},
						success: function(response)
						{
							if(response == 'not_authed')
							{
								$('#checkingAuth').hide();
								$('#noAuth').show();
							}
							else if(response == 'authed')
							{
								// with everything good to go, send the emails
								$('#passwordModal').modal('hide');

								// open the sending modal
								$('.timerMinu').text(String(minutes));
								$('.timerSecu').text(String(seconds));
								$('#emailModal').modal('show');

								// go through each mesage and send that email
								$.each(messages, function(i,id)
								{
									sendEmails(id.value,penguin);
								});
							}
						},
						error: function()
						{
							alert('Something went wrong! Please let us know by emailing support@lucolo.com');
						}
					});
				}
			});
		}
		else // if this is a gmail user, just send the emails
		{
			// open the sending modal
			$('.timerMinu').text(String(minutes));
			$('.timerSecu').text(String(seconds));
			$('#emailModal').modal('show');

			// go through each mesage and send that email
			$.each(messages, function(i,id)
			{
				sendEmails(id.value);
			});
		}	
	});

	// send the user to the email page when they close the emailSender Modal
	$('#closeEmailModalButton').click(function()
	{
		window.location = '/email/'+btoa(emailId);
	});

}); // end of doc ready