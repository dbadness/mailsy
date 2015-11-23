<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\User;
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
            if($request->_signature == 'on')
            {
                $message->message = $messageText.'<br><br>'.$user->signature;
            }else
            {
                $message->message = $messageText;
            }
            if($request->_send_to_salesforce == 'on')
            {
                $message->send_to_salesforce = 'yes';
            }
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
        Message::where('email_id',$email->id)->whereNull('deleted_at')->update(['deleted_at' => time()]);

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

    // send the emails
    public function sendEmails(Request $request)
    {
        // get the user info
        $user = Auth::user();

        // get up a gmail client connection
        $client = User::googleClient();

        // get the gmail service
        $gmail = new \Google_Service_Gmail($client);

        // send out the emails
        foreach($request->messages as $id)
        {
            $message = Message::find($id);

            // create the msg (in RFC 2822 format) so we can base64 encode it for sending through the Gmail API
            // http://stackoverflow.com/questions/24940984/send-email-using-gmail-api-and-google-api-php-client
            $email = new \PHPMailer(true); // notice the \  you have to use root namespace here
            $email->isSMTP(); // tell to use smtp
            $email->CharSet = 'utf-8'; // set charset to utf8
            $email->Subject = $message->subject;
            $email->MsgHTML($message->message);
            $email->addAddress($message->recipient);
            if($message->send_to_salesforce == 'yes')
            {
                // if they selected the 'send to salesforce' button for the email...
                $email->addBCC($user->sf_address);
            }
            $email->preSend();
            $mime = $email->getSentMIMEMessage();
            $m = new \Google_Service_Gmail_Message();
            $data = base64_encode($mime);
            $data = str_replace(array('+','/','='),array('-','_',''),$data); // url safe
            $m->setRaw($data);

            $gmail->users_messages->send('me', $m);
        }

        return redirect('/home');
    }

    // save the settings page
    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        // update the values in the DB
        User::find($user->id)->update(['sf_address' => $request->sf_address, 'signature' => $request->signature]);
        return 'success';
    }
}
