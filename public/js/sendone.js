$(document).ready(function() {

	// $('#sendOneEmailBtn').click(function()
	// {
	// 	//Check if email complaint with RFC 2822, 3.6.2.
	// 	function check(email) {
	// 		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
 //    		return re.test(email);
	// 	}

	// 	var mail = $('#email').val();

	// 	if(check(mail))
	// 	{
	// 		$('#emailTemplateHolder').val($('#emailTemplate').code());
	// 		return true;
	// 	} else
	// 	{
	// 		$('#notAnEmail').removeClass('hidden');
	// 		return false;
	// 	}

	// });

	// $('#sendFeedback').click(function(){
	// 	toastr.success('Feedback sent!');
	// });

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

