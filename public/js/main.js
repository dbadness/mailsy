
$(document).ready(function(){


	Array.prototype.clean = function(deleteValue) {
		for (var i = 0; i < this.length; i++) {
			if(this[i] == deleteValue) {         
	    			this.splice(i, 1);
		    		i--;
	    		}
			}
		return this;
	};

	//Universal toastr settings
	toastr.options.closeButton = true;
	toastr.options.preventDuplicates = true;
	toastr.options.progressBar = true;


	/**
	*
	* Signup and login pages
	* 
	*/
	// validate the signup and login forms
	$('#signupButton').click(function(e)
	{
		e.preventDefault();

		var email = $('input[name=email]').val();
		var password = $('input[name=password]').val();

		if((email == '') || (password == ''))
		{
			$('#badInfo').show();
		}
		else
		{
			$('#badInfo').hide();
			$('#authForm').submit();
		}
	});

	// refresh the fields when the user hits the button "again"
	$('#refreshFields').click(function()
	{
		// take the markup from the email and put it into the hidden textarea
		$('#emailTemplateHolder').val($('#emailTemplate').code());

		$.ajax({
			method: 'post',
			url: '/returnFields',
			data: 
			{
				'_email_template' : $('#emailTemplateHolder').val(),
				'_name' : $('input[name=_name]').val(),
				'_subject' : $('#subject').val(),
				'_token' : $('input[name=_token]').val(),
				'_email_id' : $('input[name=_email_id]').val()
			},
			error: function()
			{
				alert('Something went wrong.');
			},
			beforeSend: function() {
				$('#refreshFields').html('Loading...');
			},
			success: function(response) {
				var data = $.parseJSON(response);
				var count = 0;
				$('#saved').show();
				$('#refreshFields').html('Save Template and Refresh Fields');
				// refresh the fields div
				$('#recipientList').html('<tr id=\'headers\'><td style=\'width:40px;\'></td><td class=\'field\'><b>Email</b></td></tr><tr class=\'recipient\'><td class=\'removeRow\'><div style=\'height:5px;\'></div><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></td><td class=\'field\'><input type="text" name=\'_email[]\' class="form-control"></td></tr>');
				$.each(data.fields,function(k,v)
				{
					$('#headers').append('<td class=\'field\'><b>'+v+'</b></td>');
				});
				$.each(data.fields,function(k,v)
				{
					$('.recipient').append('<td class=\'field\'><input type="text" name="'+v+'[]" class="form-control"></td>');
				});
			}
		});
	});

	/**
	*
	* SMTP setup and testing page
	*
	*/

	// set up an email template for the user
	var itTemplate = "mailto:?cc=support@mailsy.co&subject=Email Settings&";
	itTemplate += "body=Hello,%0A%0AI'm trying to set up an internet service called Mailsy (www.mailsy.co) that works with my company email and I need some settings information about our company email. ";
	itTemplate += "The service needs to use our outgoing SMTP email server to send emails on my behalf - don't worry, it can never read, delete, or manipulate any company email.%0A%0A";
	itTemplate += "Here are the settings I'd need to get this thing up and running:%0A%0A";
	itTemplate += "SMTP Server Address (ie. smtp.example.com) %0ASMTP Username (ie. flastname) %0ASMTP Port (ie. 587) %0ASMTP Protocol (ie. TLS)"
	itTemplate += "%0A%0AThe site never saves my email password so I can take care of that. I've also copied their support email so, if you have any questions for them, they said they'd be happy to help.%0A%0AThanks!%0A";

	$('#itTemplateStatic').attr('href',itTemplate);

	// validate the form
	$('#testSmtpSettingsButton').click(function()
	{	
		var valid = false;
		// set the variables
		var server = $('input[name=smtp_server]').val();
		var uname = $('input[name=smtp_uname]').val();
		var port = $('#smtpPortSelect').val();
		var protocol = $('#smtpProtocolSelect').val();

		if((server != '') && (uname != '') && (port != '') && (protocol != ''))
		{
			$('#smtpTesterModal').modal('show');
		}
		else
		{
			$('#testError').html('Please make sure you have filled in all of the information for the email settings.');
		}
	});

	$('#sendTestEmailButton').click(function()
	{
		var server = $('input[name=smtp_server]').val();
		var uname = $('input[name=smtp_uname]').val();
		var userName = $('input[name=user_name]').val();
		var port = $('#smtpPortSelect').val();
		var protocol = $('#smtpProtocolSelect').val();
		var password = $('input[name=smtp_password]').val();
		var token = $('input[name=_token]').val();

		// send the ajax call
		$.ajax({
			url: '/smtp-tester',
			type: 'post',
			data: {
				'smtp_server': server,
				'smtp_uname': uname,
				'smtp_port': port,
				'smtp_protocol': protocol,
				'smtp_password': password,
				'_token': token
			},
			beforeSend: function()
			{
				$('#smtpTestLoader').show();
			},
			error: function()
			{
				alert('Something went wrong... please email support@mailsy.co and we\'ll help you out.');
			},
			success: function(response)
			{
				$('#smtpTestLoader').hide();

				if(response == 'success')
				{
					// close the modal and show the save settings button
					$('#smtpTesterModal').modal('hide');
					$('#testErrorDetailsWrapper').hide();
					$('#testError').html('');
					$('#saveSmtpSettingsButton').show(); // show the save button
					$('#testSmtpSettingsButton').hide(); // hide the test button
					$('#testSuccess').show();
				}
				else // if there were errors, show them and put them in a template so the user can send them to the IT dept
				{
					$('#testSuccess').hide();
					$('#smtpTesterModal').modal('hide');
					$('#testErrorDetailsWrapper').show();
					$('#testErrorDetails').html(response);

					// populate the IT team template for the user
					var templateBody = "mailto:?cc=support@mailsy.co&subject=Email Settings Errors&";
					templateBody += "body=Hello,%0A%0AI'm trying to set up an internet service called Mailsy (www.mailsy.co) that works with my company email and I don't have the settings right it seems. ";
					templateBody += "The service needs to use our outgoing SMTP email server to send emails on my behalf - don't worry, it can never read, delete, or manipulate any company email.%0A%0A";
					templateBody += "Here are the settings I've entered and the errors that I'm getting:%0A%0A";
					templateBody += " SMTP Server Address: "+server+"%0A SMTP Username: "+uname+"%0A SMTP Port: "+port+"%0A SMTP Protocol: "+protocol;
					templateBody += "%0A Error: "+response+"%0A%0AThe site never saves my email password so I can take care of that. I've copied their support email so, if you have any questions for them, they said they'd be happy to help.%0A%0ABest,%0A"+userName;

					$('#itTeamTemplateDynamic').attr('href',templateBody);
				}
			}
		});
	});


	/**
	*
	* Create/edit/use email pages
	*
	*/

	// initialise the editor
	$('#emailTemplate').summernote(
	{
		height: 300, // set editor height
	});
	// initialise the editor
	$('#signature').summernote(
	{
		height: 300, // set editor height
	});

	// Summernote, edit enter key
	$.summernote.addPlugin({
	    name : 'myenter',
	    events : {
	      // redefine insertParagraph 
	      'insertParagraph' : function(event, editor, layoutInfo) {

	        // custom enter key
	        var newLine = '<br />';
	        pasteHtmlAtCaret(newLine);

	        // to stop default event
	        event.preventDefault();
	      }
	    }
	  });

	//initial hiding of uploadData
	$('#uploadData').hide();

	// fill in the email template and sig
	// the variable is in the view
	if(typeof template !== 'undefined')
	{
		$('#emailTemplate').code(template);
	}

	// for the edit page since it's already populated
	$('#saveTemplate').click(function()
	{
		$('#emailTemplateHolder').val($('#emailTemplate').code());
	});

	// make sure that there are values in the inputs
	$('#makePreviews').submit(function()
	{
		var fields = $('#recipientList input').serializeArray();
		for(var k in fields)
		{
			if(typeof fields[k] !== 'function')
			{
				if(fields[k].value === '' && csv == '')
				{
					alert('Please make sure all your data is complete!');
					return false;
				}
			}
		}
	});

	// return the list of input fields for this template
	$('#addContacts').click(function(){

		// take the markup from the email and put it into the hidden textarea
		$('#emailTemplateHolder').val($('#emailTemplate').code());

	});

	// add another row of recipients to the list
	$('#addRecipient').click(function()
	{
		// make a global variable to duplicate the rows later
		row = $('.recipient:first').wrap('<p/>').parent().html();
		$('.recipient:first').unwrap();
		$('#recipientList').append(row);
	});

	// remove row
	$(document).on('click', '.removeRow:not(:first)', function()
	{
		$(this).parent().remove();

	});
	
	/**
	*
	*	Membership update confirmation Page
	*
	*/

	// make sure the variables in the example are still there when the user submits the email request
	$('div.note-editable.panel-body').keyup(function()
	{
		var message = $(this).code();
		var subject = $('#subject').val();

		if(subject.match(/@@company/) && message.match(/@@name/) && message.match(/@@topic/))
		{
			$('#firstEmail').show();
		}
		else
		{
			$('#firstEmail').hide();
		}
	});	

	// send the first/tutorial email
	$('#sendFirstEmail').click(function()
	{
		$.ajax({
			url: '/sendFirstEmail',
			method: 'get',
			error: function()
			{
				alert('Something went wrong. Please try again later.');
			},
			beforeSend: function()
			{
				$('#firstEmailSending').show();
			},
			success: function(response)
			{
				$('#firstEmailSending').hide();
				$('#firstEmailSent').show();
			}
		});

	});

	$('#showData').click(function()
	{
		$('#uploadData').toggle();
		$('#uploadCSV').toggle();
		$('#recipientList input').val('');
	});

	$('#showCSV').click(function()
	{
		$('#uploadData').toggle();
		$('#uploadCSV').toggle();
		$('csvFileUpload').val(null);
	});

	$('#sendListStep1').click(function()
	{
		$('#emailTemplateHolder').val($('#emailTemplate').code());
		// $('#sendListStep1').toggle();
		// $('#uploadCSV').removeClass('hidden');
		// $("#subject").prop('disabled', true);
		// $("#emailTemplate").prop('disabled', true);

		$.ajax({
			method: 'post',
			url: '/returnFieldsOneOff',
			data: 
			{
				'_email_template' : $('#emailTemplateHolder').val(),
				'_name' : $('#name').val(),
				'_subject' : $('#subject').val(),
				'_token' : $('input[name=_token]').val(),
			},
			error: function(response)
			{
				// alert('Something went wrong.');
				console.log(response);
			},
			beforeSend: function() {
				$('#refreshFields').html('Loading...');
			},
			success: function(response) {
				window.location = response;
			}
		});

	});

	//convert those marked to convert to proper time
	window.onload = function()
	{
		function timeConverter(UNIX_timestamp){
			var a = new Date(UNIX_timestamp * 1000);
			var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			var year = a.getFullYear();
			var month = months[a.getMonth()];
			var date = a.getDate();
			var hour = a.getHours();
			var min = a.getMinutes();
			var sec = a.getSeconds();
			var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
  			return time;
		}

		$('.unixToConvert').each(function()
		{
			$(this).text(timeConverter($(this).code()));
		})

	};

}); // end doc ready

// https://github.com/summernote/summernote/issues/702
function pasteHtmlAtCaret(html) {
    var sel, range;
    if (window.getSelection) {
        // IE9 and non-IE
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();

            // Range.createContextualFragment() would be useful here but is
            // only relatively recently standardized and is not supported in
            // some browsers (IE9, for one)
            var el = document.createElement("div");
            el.innerHTML = html;
            var frag = document.createDocumentFragment(), node, lastNode;
            while ( (node = el.firstChild) ) {
                lastNode = frag.appendChild(node);
            }
            range.insertNode(frag);

            // Preserve the selection
            if (lastNode) {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
    } else if (document.selection && document.selection.type != "Control") {
        // IE < 9
        document.selection.createRange().pasteHTML(html);
    }
}