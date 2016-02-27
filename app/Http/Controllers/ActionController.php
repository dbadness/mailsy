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
use File;

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

            // trim the <p> tags off the messageText
            $messageText = substr($messageText,0,-4);
            $messageText = substr($messageText,3);

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

                // prepend the read receipt callback webhook to the message
                $full_body = $message->message.'<img src="'.env('DOMAIN').'/track/'.base64_encode($user->id).'/'.base64_encode($message->id).'">';

                // use swift mailer to build the mime
                $mail = new \Swift_Message;
                $mail->setFrom(array($user->email => $user->name));
                $mail->setTo([$message->recipient]);
                $mail->setBody($full_body, 'text/html');
                $mail->setSubject($message->subject);
                if($message->send_to_salesforce)
                {
                    // if they selected the 'send to salesforce' button for the email...
                    $mail->addBCC($user->sf_address);
                }
                $data = base64_encode($mail->toString());
                $data = str_replace(array('+','/','='),array('-','_',''),$data); // url safe
                $m = new \Google_Service_Gmail_Message();
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

        return redirect('/email/'.base64_encode($email->id));
    }

    // save the settings page
    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        // update the values in the DB
        $user->name = $request->name;
        $user->sf_address = $request->sf_address;
        $user->signature = $request->signature;
        if($request->track_email == 'yes')
        {
            $user->track_email = 'yes';
        }
        else
        {
            $user->track_email = NULL;
        }
        
        $user->save();

        return 'success';
    }

    // upgrade the user to a paid account (and send out invites to users if need be)
    public function doUpgrade(Request $request, $add = null)
    {
        // auth the user
        $user = Auth::user();

        // get the count of users that are being charged for their accounts
        $userCount = 0;

        if($request->myself == 'on')
        {
            $userCount++;
            $user->paid = 'yes';
        }

        if($request->newusers)
        {
            // tell the DB that this is an admin type user
            $user->has_users = 'yes';
            $count = count($request->newusers);
            $userCount = $userCount + $count;
        }

        // attempt to charge their card via stripe
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

        // for the emails
        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));

        // if this a new subscription...
        if(!$add)
        {
            // make a new customer if this is their first time upgrading
            if(!$user->stripe_id)
            {
                // Use Stripe's library to make requests...
                $customer = \Stripe\Customer::create(array(
                    'source' => $request->stripe_token,
                    'plan' => 'paid',
                    'email' => $user->email,
                    'quantity' => $userCount
                ));

                $user->stripe_id = $customer->id;
            }
            else
            {
                // if they're an existing user, just create a new subscription for  them
                $customer = \Stripe\Customer::retrieve($user->stripe_id);
                $customer->subscriptions->create(array("plan" => "paid"));
            }
            
            $user->status = 'paying';
            $user->expires = null; // in case they reupgrade before their subscription exprires

            // the email will be sent by the stripe webhook
        }
        else // if they're adding to a pre-existing subscription
        {
            // add the user new count to the quantity of the subscription
            $stripeUser = \Stripe\Customer::retrieve($user->stripe_id);
            $quantity = (int) $stripeUser->subscriptions->data{0}->quantity;
            $subscription = $stripeUser->subscriptions->retrieve($stripeUser->subscriptions->data{0}->id);
            $subscription->quantity = $quantity + $userCount;
            $subscription->save();

            // create an invoice on this subsription with the new users at the prorated rate
            $stripeUser = \Stripe\Customer::retrieve($user->stripe_id);
            $invoice = \Stripe\Invoice::create(array(
                'customer' => $user->stripe_id,
                'subscription' => $stripeUser->subscriptions->data{0}->id
            ));

            // pay that invoice with the card that's on file
            $invoice = \Stripe\Invoice::retrieve($invoice->id);
            $invoice->pay();

            // send this user an email to confirm the upgrade
            // let the user know that they've updated their card
            // the email body
            if($userCount == 1)
            {
                $descriptor = 'person';
            }
            else
            {
                $descriptor = 'people';
            }
            $body = 'Hi '.$user->email.',<br><br>You\'ve successully added '.$userCount.' new '.$descriptor.' to your Mailsy subscription.<br><br>';
            $body .= 'If you have any questions, please send an email to <a href="mailto:hello@mailsy.co">hello@mailsy.com</a> and we\'d be happy to help.<br><br>';
            $body .= 'Thank you,<br>The Mailsy Team';

            $data = array(
                "id" => 5, // blank template
                "to" => $user->email,
                "attr" => array(
                    "SUBJECT" => 'You\'ve added '.$userCount.' '.$descriptor.' to your Mailsy subscription',
                    "TITLE" => 'You\'ve successfully added '.$descriptor.' to your Mailsy subscription!',
                    'BODY' => $body
                )
            );

            $mailin->send_transactional_template($data);
        }

        // save the user's attributes
        $user->save();
        
        // if there are multiple users, sign them up and mark them as paid users
        if($request->newusers)
        {
            foreach($request->newusers as $newuser)
            {
                if(!User::where('email',$newuser)->first())
                {
                    // create the new user
                    $newuserObject = new User;
                    $newuserObject->email = $newuser;
                    $newuserObject->paid = 'yes';
                    $newuserObject->belongs_to = $user->id;
                    $newuserObject->created_at = time();
                    $newuserObject->save(); 
                }
                else
                {
                    // get the existing member
                    $existingUser = User::where('email',$newuser)->first();
                    $existingUser->paid = 'yes';
                    $existingUser->belongs_to = $user->id;
                    $existingUser->save(); 
                }
                

                // send the user an email and let them know they've been signed up
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
        // get the message object
        $message = Message::find($id);

        return ucfirst($message->status);
    }

    // update a customer card
    public function doUpdateCard(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // create a new card object
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

        $cu = \Stripe\Customer::retrieve($user->stripe_id);
        $card = $cu->sources->create(array("source" => $request->stripe_token));

        // update the 'default_source' of the customer for future invoices
        $cu->default_source = $card->id;
        $cu->save();

        // let the user know that they've updated their card
        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));

        // the email body
        $body = 'Hi '.$user->email.',<br><br>Your payment method (ending in '.$card->last4.') has been successully added to your account.<br><br>';
        $body .= 'If you have any questions, please send an email to <a href="mailto:hello@mailsy.co">hello@mailsy.com</a> and we\'d be happy to help.<br><br>';
        $body .= 'Thank you,<br>The Mailsy Team';

        $data = array(
            "id" => 5, // blank template
            "to" => $user->email,
            "attr" => array(
                "SUBJECT" => 'Payment method updated for Mailsy',
                "TITLE" => 'Payment method successfully updated!',
                'BODY' => $body
            )
        );

        $mailin->send_transactional_template($data);

        return json_encode($card);
    }

    // update/cancel memberships
    public function doCancelMembership(Request $request, $master = null)
    {
        // set the stripe token
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

        // auth the user
        $user = Auth::user();

        // retrieve the subscription info
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $subscription = $customer->subscriptions->retrieve($customer->subscriptions->data{0}->id);

        if($master)
        {
            if($user->has_users)
            {
                // get the users that are associated with this admin user
                $children = User::where('belongs_to',$user->id)->whereNull('deleted_at')->get();
                // make everyone a free user at the end of the subscription period
                foreach($children as $child)
                {   
                    // update this users expiration date and remove the relationship to this admin
                    $success = User::where('id',$child->id)->update(['expires' => $subscription->current_period_end, 'belongs_to' => NULL]);

                    // send the child an email letting them know that their admin cancelled their subscription
                } 
            }

            // cancel the subscription
            $canceledSubscription = $subscription->cancel(['at_period_end' => true]);
            $user->expires = time();
            $user->has_users = null;
            $user->status = null;
            $user->save();

            // send an email to the admin letting them know they're unsubscribed

            // success message
            return 'This subscription was canceled at '.$canceledSubscription->cancel_at_period_end;
        }
        else
        {
            // decrement the subscription quantity
            $subscription->quantity = $subscription->quantity - 1;
            $subscription->save();

            // if this is an admin cancelling a members subscription
            if($request->ref)
            {
                $member = User::find(substr(base64_decode($request->ref),0,-5));
                // update this members attrs
                $member->belongs_to = null;
                $member->save();
            }

            // if this was the last member this admin manages, update their attr accordingly
            $memberCount = User::where('belongs_to',$user->id)->count();
            if($memberCount == 0)
            {
                $user->has_users = null;
                
                // if the admin isn't a paid user and they were just paying for someone else, cancel the subscription
                if(!$user->paid)
                {
                    $canceledSubscription = $subscription->cancel(['at_period_end' => true]);
                    $user->status = null;
                }

                // save the settings
                $user->save();
            }

            // send an email to the admin letting them know that the update has been successful

            // send an email to the user letting them know their account has been downgraded

            // success message
            return 'Subscription quantity is now '.$subscription->quantity;
        }
    }

    // send feedback on 500 page
    public function doSendFeedback(Request $request)
    {
        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
        $data = array( 
            "to" => array("dave@mailsy.co"=>"David Baines"),
            "from" => array('dave@mailsy.co','Mailsy'),
            "subject" => '500 Error Feedback',
            "html" => 'Feedback: '.$request->feedback
        );
        
        $mailin->send_email($data);

        return redirect('/home');
    }

    // send the tutorial email to the user
    public function doSendFirstEmail()
    {
        $user = Auth::user();

        $subject = 'Working with Example Co, Inc';
        $body = 'Hi Steve,<br><br>';
        $body .= 'Name is Alex and we met last night at the event and spoke briefly about getting more users to your site. ';
        $body .= 'I thought we had a great conversation and wanted to follow up on that. Could we set up a time to speak sometime this week?';
        $body .= '<br><br>Thank you for your time and let me know when you\'d like to connect and I\'d be happy to block it out.';
        $body .= '<br><br>Best,<br>Alex';

        // get up a gmail client connection
        $client = User::googleClient();

        // get the gmail service
        $gmail = new \Google_Service_Gmail($client);

        // use swift mailer to build the mime
        $mail = new \Swift_Message;
        $mail->setTo([$user->email]);
        $mail->setBody($body, 'text/html');
        $mail->setSubject($subject);
        $data = base64_encode($mail->toString());
        $data = str_replace(array('+','/','='),array('-','_',''),$data); // url safe
        $m = new \Google_Service_Gmail_Message();
        $m->setRaw($data);
        $gmailMessage = $gmail->users_messages->send('me', $m);

        // update the DB so we can check if this feature is used
        $user->tutorial_email = 'yes';
        $user->save();

        return 'success';
    }

    // webhook for emails opened by the recipients (read receipts) and returns an image to fool the email
    // we'll also need the user id since this webhook is stateless
    public function doTrack($e_user_id, $e_message_id)
    {
        // decrypt the ids
        $user_id = base64_decode($e_user_id);
        $message_id = base64_decode($e_message_id);

        // get the message id and make the DB update
        $message = Message::find($message_id);

        if($message->status != 'read')
        {
            $user = Auth::loginUsingId($user_id);

            $message->status = 'read';
            $message->read_at = time();
            $message->save();

            if($user->track_email)
            {
                // set the timezone
                date_default_timezone_set('EST');

                // end a test email
                $subject = $message->recipient.', opened your Mailsy email!';
                $body = 'Hi there,<br><br>';
                $body .= 'We\'re writing to let you that '.$message->recipient.' opened your email on '.date('D, M d, Y', $message->read_at).' at '.date('g:ia',$message->read_at).' EST.';
                $body .= '<br><br>Best,<br>The Mailsy Team';

                $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
                $data = array( 
                    "to" => array($user->email => $user->name),
                    "from" => array('no-reply@mailsy.co','Mailsy'),
                    "subject" => $subject,
                    "html" => $body
                );
                
                $mailin->send_email($data);
            }
        }

        return File::get('images/email-tracker.png');
    }

}
