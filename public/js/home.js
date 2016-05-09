$(document).ready(function()
{
	
	// cycle through the emails and return the response rates for them and insert them in the document (so the page loads faster)
	var emails = $('#emails input').serializeArray();

	$.each(emails, function(i,email)
	{
		$.ajax({
			method: 'get',
			url: '/getReplyRate/' + email.value,
			beforeSend: function()
			{
				$('#replyRateForEmail' + email.value).html('<img src="/images/loader.gif">');
			},
			error: function()
			{
				alert: 'Something went wrong getting your email reply rates. Please check back later.';
			},
			success: function(response)
			{
				$('#replyRateForEmail' + email.value).html(response);
			}
		});
	});

}); // end doc ready