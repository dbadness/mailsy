$(document).ready(function()
{
	// this updates all the messages in the list from the Gmail server
	// build the array of the message IDs
	var ids = $('input').serializeArray();
	var length = ids.length;
	var increment = Number((100/length).toFixed(2));
	var total = 0;
	var count = 0;

	// pull down the status and let the view know what the progress is
	$.each(ids, function(i,id)
	{	
		$.ajax({
			url: '/getMessageStatus/'+id.value,
			success: function(response){
				// increment the progress bar
				total += increment;
				$('.progress-bar').css('width',total.toFixed(0)+'%');
				$('#progressText').html(total.toFixed(0)+'% Complete');
				// update the view with the status
				$('#status'+id.value).html(response);
			},
			error: function(){
				alert('Something went wrong.');
			}
		});
	});

	// show and hide the emails
	$('.viewMessage').click(function()
	{
		// hide any open messages
		$('.messageView').hide();
		$('tr').css('border','none');
		// find the message we want to show
		var id = $(this).attr('messageId');
		// and show that message tray
		$('#message'+id).show();
		// throw some borders on there
		var row = $(this).closest('tr');
		row.css('border','solid 2px black');
		row.next().css('border','solid 2px black');
	});

	$('.close').click(function()
	{
		// hide any open messages
		$('.messageView').hide();
		$('tr').css('border','none');
	});

});	// end of doc ready
