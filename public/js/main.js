$(document).ready(function(){

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