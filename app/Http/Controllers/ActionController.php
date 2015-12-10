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
use Redirect;

// for SendinBlue
use \Sendinblue\Mailin as Mailin;


class ActionController extends Controller
{
    // return the fields to the new email view from the ajax call with template
    public function returnFields(Request $request)
    {
        // make sure that all the fields are accounted for and are alphanumeric
        if(!$request->_name || !$request->_subject || !$request->_email_template)
        {
            return 'no main content';
        }

        // save the email template
        $user = Auth::user();

        // if there's no email_id, create one. if there is, use it
        if(!$request->_email_id)
        {
            // create the email object
            $email = new Email;
            $email->user_id = $user->id;
            $email->name = $request->_name;
            $email->subject = $request->_subject;
            $email->template = $request->_email_template;
            $email->created_at = time();

            $email->save();
        }
        else
        {
            $email = Email::find($request->_email_id);
        }

        // combine the subject and template for regex matching
        $content = $request->_subject.' '.$request->_email_template;

        // find the variables in the email and return them to the view        
        preg_match_all('/@@[a-zA-Z0-9]*/',$content,$matches);

        if($matches)
        {
            foreach($matches as $k => $v)
            {
                $fields = [];
                foreach($v as $match)
                {
                    // shave the delimiters
                    $field = trim($match,'@@');
                    $fields[] = $field;
                }

                // save the fields to the DB
                $email->fields = json_encode($fields);
                $email->save();
            }
            return json_encode(['fields' => $fields, 'email' => $email->id]);
        }
        else
        {
            return json_encode(['email' => $email->id]);
        }
    }

    // save the template if the user edits it
    public function saveTemplate(Request $request)
    {

        // combine the subject and template for regex matching
        $content = $request->_subject.' '.$request->_email_template;

        // find the variables in the email and return them to the view        
        preg_match_all('/@@[a-zA-Z0-9]*/',$content,$matches);

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
        }

        // save the email template
        $email = Email::find($request->_email_id);
        $email->name = $request->_name;
        $email->subject = $request->_subject;
        $email->template = $request->_email_template;
        $email->fields = json_encode($fields);
        $email->save();

        // send the user to the 'use' view
        return redirect('/use/'.base64_encode($email->id));
    }

    // take the template's contents and the recipients list and generate previews for the user
    public function makePreviews(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // find the email object
        $email = Email::find($request->_email_id);

        // build the recipient list and assign the fields to them
        $messages = [];
        $tempRecipientsList = [];
        foreach($_POST['_email'] as $key => $recipientEmail)
        {

            // return the array of the fields from the user
            $fields = [];
            foreach($_POST as $k => $v)
            {
                if(($k != 'files') && (substr($k,0,1) != '_'))
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

              // make sure the emails are legit
        foreach($request->_email as $recipientEmail)
        {
            if(!filter_var($recipientEmail,FILTER_VALIDATE_EMAIL))
            {
                return redirect('/edit/'.base64_encode($email->id).'?badEmails=true');
            }
            else
            {
                return redirect('/preview/'.base64_encode($email->id));
            }
        }
    }

    /*
    // take the template's contents and the recipients list and generate previews for the user upon updating the email
    public function updatePreviews(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // fetch the email from the DB
        $email = Email::find($request->_email_id);

        // first delete all the messages previous unsent in this email (since the user is updating them)
        // you can compare the made:sent ratios later to determine how many times users need to edit emails
        Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->update(['deleted_at' => time()]);

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
    */
    
    // send the emails
    public function sendEmails(Request $request)
    {
        // get the user info
        $user = Auth::user();

        // find the email object and delete and temp_recipients_list
        $email = Email::find($request->email_id);
        $email->temp_recipients_list = null;
        $email->save();

        // get up a gmail client connection
        $client = User::googleClient();

        // get the gmail service
        $gmail = new \Google_Service_Gmail($client);

        // send out the emails
        foreach($request->messages as $id)
        {

            // if they're not a paid user, make sure they don't send more than 10 emails per day
            $emailsLeft = User::howManyEmailsLeft();
            if($emailsLeft > 0)
            {
                $message = Message::find($id);

                // create the msg (in RFC 2822 format) so we can base64 encode it for sending through the Gmail API
                // http://stackoverflow.com/questions/24940984/send-email-using-gmail-api-and-google-api-php-client
                $mail = new \PHPMailer(true); // notice the \  you have to use root namespace here
                $mail->isSMTP(); // tell to use smtp
                $mail->CharSet = 'utf-8'; // set charset to utf8
                $mail->Subject = $message->subject;
                $mail->MsgHTML($message->message);
                $mail->setFrom($user->email, $user->name); // set from attr
                $mail->addAddress($message->recipient);
                if($message->send_to_salesforce == 'yes')
                {
                    // if they selected the 'send to salesforce' button for the email...
                    $mail->addBCC($user->sf_address);
                }
                $mail->preSend();
                $mime = $mail->getSentMIMEMessage();
                $m = new \Google_Service_Gmail_Message();
                $data = base64_encode($mime);
                $data = str_replace(array('+','/','='),array('-','_',''),$data); // url safe
                $m->setRaw($data);

                $gmailMessage = $gmail->users_messages->send('me', $m);

                // insert the returned google message id into the DB and mark it as sent
                $message->google_message_id = $gmailMessage->id;
                $message->status = 'sent';
                $message->sent_at = time();
                $message->save();
            }
            else
            {
                // delete all unsent emails (the user has been warned)
                Message::where('id',$id)->update(['deleted_at' => time()]);
            }
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

    // upgrade the user to a paid account (and send out invites to users if need be)
    public function doUpgrade(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // get the count of users that are being charged for their accounts
        $userCount = 0;

        if($request->myself == 'on')
        {
            $userCount++;
            $user->paid = 'yes';
            $user->save();
        }

        if($request->newusers)
        {
            $count = count($request->newusers);
            $userCount = $userCount + $count;
        }

        // attempt to charge their card via stripe
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

        // Use Stripe's library to make requests...
        $customer = \Stripe\Customer::create(array(
            'source' => $request->stripe_token,
            'plan' => 'paid',
            'email' => $user->email,
            'quantity' => $userCount
        ));
        
        // if there are multiple users, sign them up and mark them as paid users
        if($request->newusers)
        {
            foreach($request->newusers as $newuser)
            {
                // create the new user
                $newuserObject = new User;
                $newuserObject->email = $newuser;
                $newuserObject->paid = 'yes';
                $newuserObject->belongs_to = $user->id;
                $newuserObject->created_at = time();
                $newuserObject->save();

                // send the user an email and let them know they've been signed up
                $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
                $data = array(
                    "id" => 2,
                    "to" => $newuser,
                    "attr" => array("CUSTOMER"=>$newuser,"FROM"=>$user->email)
                );

                $mailin->send_transactional_template($data);
            }
        }

        // send the admin to the settings page so they can see how to manage the members they signed up
        return redirect('/settings?message=upgradeSuccess');
    }

    // requests, updates, and return the message status
    public function doUpdateMessageStatus($id)
    {
        $status = Message::updateMessageStatus($id);
        return ucfirst($status);
    }

}
