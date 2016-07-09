$(document).ready(function() {

	function validateEmail(email) {
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

	$("#recipientTags").tagit({
		beforeTagAdded: function(event, ui){
			if(validateEmail(ui.tag[0].textContent.slice(0, -1)))
			{
				// console.log(ui.tag[0].textContent.slice(0, -1));
				return true;
			} else{
				toastr.error('Not an email!');
				return false;
			}
		}
	});
	$("#CCTags").tagit({
		beforeTagAdded: function(event, ui){
			if(validateEmail(ui.tag[0].textContent.slice(0, -1)))
			{
				// console.log(ui.tag[0].textContent.slice(0, -1));
				return true;
			} else{
				toastr.error('Not an email!');
				return false;
			}
		}
	});
	$("#BCCTags").tagit({
		beforeTagAdded: function(event, ui){
			if(validateEmail(ui.tag[0].textContent.slice(0, -1)))
			{
				// console.log(ui.tag[0].textContent.slice(0, -1));
				return true;
			} else{
				toastr.error('Not an email!');
				return false;
			}
		}
	});

	$("#sendOneEmailBtn").click(function(){
		$('#emailTemplateHolder').val($('#emailTemplate').code());


			$('form').submit(function() {
				var penguin = $('#password').val();

				// validate the password field
				if(penguin ==  '')
				{
					toastr.error('No password entered');
					return false;
				}
				else if(penguin == undefined)
				{

				}
				else
				{
					penguin = window.btoa(penguin);

					// check to see if the password is correct
					$.ajax({
						url: '/smtp-auth-check/'+penguin,
						type: 'get',
						beforeSend: function()
						{
							// $('#checkingAuth').show();
						},
						success: function(response)
						{
							if(response == 'not_authed')
							{
								toastr.error('Incorrect Password');
								return false;
							}
							else if(response == 'authed')
							{
								document.getElementById("sendOneEmail").submit();
								// return true;
								// // with everything good to go, send the emails
								// $('#passwordModal').modal('hide');

								// // open the sending modal
								// $('.timerMinu').text(String(minutes));
								// $('.timerSecu').text(String(seconds));
								// $('#emailModal').modal('show');

								// // go through each mesage and send that email
								// $.each(messages, function(i,id)
								// {
								// 	sendEmails(id.value,penguin);
								// });
							}
						},
						error: function()
						{
							toastr.error('Something went wrong! Please let us know by emailing support@lucolo.com');
							return false;
						}
					});
					return false;
				}
			});

		var prTags = [];
		var pcTags = [];
		var pbTags = [];

		var rTags = $("#recipientTags");
		rTags.each(function(idx, li) {
    		var holder = $(li);
    		var processor = holder[0].outerText;
    		processor = processor.split(/\n/)
    		processor.clean("");

    		prTags = processor;
		});

		if(prTags.length === 0)
		{
			if($('#email')[0] != null)
			{
				if($('#email')[0].value == "")
				{
					toastr.error('There is no valid "to" email!');
					return false;
				} else{
					toastr.error('There is no valid "to" email!');
					return false;				
				}
			} else{
				toastr.error('There is no valid "to" email!');
				return false;				
			}
		}

		if($('#subject')[0].value == "")
		{
			toastr.error('There is no subject!');
			return false;
		}

		var cTags = $("#CCTags");
		cTags.each(function(idx, li) {
    		var holder = $(li);
    		var processor = holder[0].outerText;
    		processor = processor.split(/\n/)
    		processor.clean("");

    		pcTags = processor;
		});

		var bTags = $("#BCCTags");
		bTags.each(function(idx, li) {
    		var holder = $(li);
    		var processor = holder[0].outerText;
    		processor = processor.split(/\n/)
    		processor.clean("");

    		pbTags = processor;

		});

		for( var i = 0, len = prTags.length; i < len; i++ )
		{
			$('<input>').attr({
    			type: 'hidden',
    			value: prTags[i],
    			name: '_recipient[]'
			}).appendTo('#sendOneEmail');
		}

		for( var i = 0, len = pcTags.length; i < len; i++ )
		{
			$('<input>').attr({
    			type: 'hidden',
    			value: pcTags[i],
    			name: '_cc[]'
			}).appendTo('#sendOneEmail');
		}

		for( var i = 0, len = pbTags.length; i < len; i++ )
		{
			$('<input>').attr({
    			type: 'hidden',
    			value: pbTags[i],
    			name: '_bcc[]'
			}).appendTo('#sendOneEmail');
		}

		return true;

	});
});

