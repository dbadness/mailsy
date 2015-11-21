<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Email;
use App\Message;
use App\Recipient;
use App\Field;


class ActionController extends Controller
{
    // return the fields to the new email view from the ajax call with template
    public function returnFields(Request $request)
    {
        // find the variables in the email and return them to the view        
        preg_match_all('/@@[a-zA-Z0-9]*/',$request->_content,$matches);

        if($matches)
        {
            foreach($matches as $k => $v)
            {
                $fields = [];
                foreach($v as $match)
                {
                    // shave the delimiters
                    $field = trim($match,'@@');
                    $fields[] = strtolower($field);
                }
            }
            return json_encode($fields);
        }
        else
        {
            return 'No matches found.';
        }
    }

    // take the template's contents and the recipients list and generate previews for the user
    public function makePreviews(Request $request)
    {
        $user = Auth::user();

        // create the email object
        $email = new Email;
        $email->user_id = $user->id;
        $email->name = $request->_name;
        $email->subject = $request->_subject;
        $email->template = $request->_email_template;
        $email->created_at = time();

        $email->save();

        // build the recipient list and assign the fields to them
        $messages = [];
        $tempRecipientsList = [];
        foreach($_POST['_email'] as $key => $recipientEmail)
        {

            // return the array of the fields from the user
            $fields = [];
            foreach($_POST as $k => $v)
            {
                if(substr($k,0,1) != '_')
                {
                    $fields[] = $k;
                }
            }

            // for each field provided, replace the variable in the template with the correct field input
            // use the key we returned from figuring out with recipient entry we're currently on
            $messageText = $request->_email_template;
            $subjectText = $request->_subject;
            $fieldEntries = [];
            foreach($fields as $field)
            {
                $subjectText = str_replace('@@'.$field, $_POST[$field][$key], $subjectText);
                $messageText = str_replace('@@'.$field, $_POST[$field][$key], $messageText);
                // set up an entry for the recipients list later on
                $fieldEntries[] = [$field => $_POST[$field][$key]];
            }

            // make a message to throw into the DB
            $message = new Message;
            $message->user_id = $user->id;
            $message->email_id = $email->id;
            $message->recipient = $recipientEmail;
            $message->subject = $subjectText;
            $message->message = $messageText;
            $message->created_at = time();
            $message->save();

            // set up the data list in case the user wants to go back and make some edits
            $tempRecipientsList[] = [
                '_email' => $recipientEmail,
                '_fields' => json_encode($fieldEntries)
            ];
            
        }

        // save the tempRecipientsList to the email object for future use (if needed)
        $email->temp_recipients_list = json_encode($tempRecipientsList);
        $email->save();

        return redirect('/preview/'.base64_encode($email->id));
    }

    // take the template's contents and the recipients list and generate previews for the user upon updating the email
    public function updatePreviews(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // fetch the email from the DB
        $email = Email::find($request->_email_id);

        // first delete all the messages previous unsent in this email (since the user is updating them)
        Message::where('email_id',$email->id)->update(['deleted_at' => time()]);

        // build the recipient list and assign the fields to them
        $messages = [];
        $tempRecipientsList = [];
        foreach($_POST['_email'] as $key => $recipientEmail)
        {

            // return the array of the fields from the user
            $fields = [];
            foreach($_POST as $k => $v)
            {
                if(substr($k,0,1) != '_')
                {
                    $fields[] = $k;
                }
            }

            // for each field provided, replace the variable in the template with the correct field input
            // use the key we returned from figuring out with recipient entry we're currently on
            $messageText = $request->_email_template;
            $subjectText = $request->_subject;
            $fieldEntries = [];
            foreach($fields as $field)
            {
                $subjectText = str_replace('@@'.$field, $_POST[$field][$key], $subjectText);
                $messageText = str_replace('@@'.$field, $_POST[$field][$key], $messageText);
                // set up an entry for the recipients list later on
                $fieldEntries[] = [$field => $_POST[$field][$key]];
            }

            // make a message to throw into the DB
            $message = new Message;
            $message->user_id = $user->id;
            $message->email_id = $email->id;
            $message->recipient = $recipientEmail;
            $message->subject = $subjectText;
            $message->message = $messageText;
            $message->created_at = time();
            $message->save();

            // set up the data list in case the user wants to go back and make some edits
            $tempRecipientsList[] = [
                '_email' => $recipientEmail,
                '_fields' => json_encode($fieldEntries)
            ];

            // save the tempRecipientsList to the email object for future use (if needed)
            $email->temp_recipients_list = json_encode($tempRecipientsList);
            $email->save();

        }

        // send to the preview page
        return redirect('/preview/'.base64_encode($email->id));
    }
}
