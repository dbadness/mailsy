<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Email;
use App\Message;
use App\Customer;
use App\Utils;
use Redirect;
use Log;
use File;
use Response;

class ActionController extends Controller
{

    // send a test email when the user sets up their smtp settings
    public function doSmtpTester(Request $request)
    {

        // Create the Transport
        try
        {
            $transport = \Swift_SmtpTransport::newInstance($request->smtp_server, $request->smtp_port, $request->smtp_protocol)->setUsername($request->smtp_uname)->setPassword($request->smtp_password);

            $mailer = \Swift_Mailer::newInstance($transport);

            $mail = new \Swift_Message;

            // Create a message
            $subject = 'Test email from Mailsy.';

            $body = 'Hi there,<br><br>Looks like everything is set up and working correctly! You can now <a href="'.env('DOMAIN').'/smtp-setup">save your email settings on Mailsy</a> and start sending out emails en masse!<br><br>- The Mailsy Team';

            // get the email of the user
            $user = Auth::user();

            $mail->setFrom(array($user->email));
            $mail->setTo([$user->email => $user->name]);
            $mail->setBody($body, 'text/html');
            $mail->setSubject($subject);

            $result = $mailer->send($mail);
        }
        catch(\Swift_TransportException $e)
        {
            return $e->getMessage();
            die;
        }

        // if we made it this far, return success
        return 'success';

    }

    // if the test email is successful, save the smtp settings for the user
    public function doSmtpSave(Request $request)
    {
        $user = Auth::user();

        $user->smtp_server = $request->smtp_server;
        $user->smtp_uname = $request->smtp_uname;
        $user->smtp_port = $request->smtp_port;
        $user->smtp_protocol = $request->smtp_protocol;

        $user->save();

        return redirect('/tutorial/step1');
    }

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
            $email->creator_name = $user->name;
            $email->created_at = time();
            $email->shared = 0;
            $email->copies = 0;
            if($user->admin)
            {
                $email->creator_company = $user->id;
            } else
            {
                $email->creator_company = $user->belongs_to;
            }
            $email->save();
        }
        else
        {
            $email = Email::find($request->_email_id);
            $email->name = $request->_name;
            $email->subject = $request->_subject;
            $email->template = $request->_email_template;
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
                $fields = array_unique($fields, SORT_REGULAR);
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

    public function createTemplate(Request $request)
    {
        // make sure that all the fields are accounted for and are alphanumeric
        if(!$request->_name || !$request->_subject || !$request->_email_template)
        {
            return 'no main content';
        }
        // save the email template
        $user = Auth::user();

        // create the email object
        $email = new Email;
        $email->user_id = $user->id;
        $email->name = $request->_name;
        $email->subject = $request->_subject;
        $email->template = $request->_email_template;
        $email->created_at = time();
        $email->creator_name = $user->name;
        $email->shared = 0;
        $email->copies = 0;
        if($user->admin)
        {
            $email->creator_company = $user->id;
        } else
        {
            $email->creator_company = $user->belongs_to;
        }
        $email->save();

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
                $fields = array_unique($fields, SORT_REGULAR);
                // save the fields to the DB
                $email->fields = json_encode($fields);
                $email->save();
            }
            return redirect('/use/'.base64_encode($email->id));
        }
        else
        {
            return redirect('/use/'.base64_encode($email->id));
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
                $fields = array_unique($fields, SORT_REGULAR);
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

        // split on whether there's a CSV or not
        if($request->csvFile)
        {
            return $response = Email::processCSV($request, $email, $user);
        } 
        else
        {
            return Email::processManualData($request, $email, $user);
        }
    }
    
    // send the emails
    public function sendEmail($email_id, $message_id)
    {
        // get the user info
        $user = Auth::user();
        // find the email object and delete and temp_recipients_list
        $email = Email::find($email_id);
        $email->temp_recipients_list = null;
        $email->save();
        // send out the email

        // if they're not a paid user, make sure they don't send more than 10 emails per day
        $emailsLeft = User::howManyEmailsLeft();
        if($emailsLeft > 0)
        {

            // set the message up
            $message = Message::find($message_id);
            // prepend the read receipt callback webhook to the message

            $full_body = $message->message.'<img src="'.env('DOMAIN').'/track/'.base64_encode($user->id).'/'.base64_encode($message->id).'" alt="tracker" title="tracker" style="display:block" width="1" height="1">';

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

            // send out the message based on their email setup
            if($user->gmail_user = 1)
            {
                // get up a gmail client connection
                $client = User::googleClient();

                // get the gmail service
                $gmail = new \Google_Service_Gmail($client);

                // make the message RFC compliant
                $data = base64_encode($mail->toString());
                $data = str_replace(array('+','/','='),array('-','_',''),$data); // url safe
                $m = new \Google_Service_Gmail_Message();
                $m->setRaw($data);
                $gmailMessage = $gmail->users_messages->send('me', $m);
                // insert the returned google message id into the DB and mark it as sent
                $message->google_message_id = $gmailMessage->id;
            }
            else // if they're using their own companies SMTP server...
            {
                // build the transport mechanism
                $transport = \Swift_SmtpTransport::newInstance($request->smtp_server, $request->smtp_port, $request->smtp_protocol)
                ->setUsername($request->smtp_uname)
                ->setPassword($request->smtp_password);

                $mailer = \Swift_Mailer::newInstance($transport);
            
                // send the email from the messages above
                $result = $mailer->send($mail);
            }

            // save the message info now that the emails have been sent
            $message->status = 'sent';
            $message->sent_at = time();
            $message->save();

        }
        else
        {
            // delete all unsent emails (the user has been warned)
            Message::where('id',$email->id)->update(['deleted_at' => time()]);
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
        $user->timezone = $request->timezone;

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
    public function doUpgrade(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // attempt to charge their card via stripe
        // See your keys here https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

        // if this a new subscription...
        // make a new customer if this is their first time upgrading
        if(!$user->stripe_id)
        {
            // Use Stripe's library to make requests...
            $customer = \Stripe\Customer::create(array(
                'source' => $request->stripe_token,
                'plan' => 'paid',
                'email' => $user->email,
                'quantity' => 1
            ));

            // set their stripe id and their payment settings
            $user->stripe_id = $customer->id;
            $user->status = 'paying';
            $user->paid = 'yes';
            $user->expires = null; // in case they reupgrade before their subscription exprires
            $user->save();
        }
        else
        {
            // return their existing stripe key and handle the 'resignup' if that's the case based on an expiration
            $customer = \Stripe\Customer::retrieve($user->stripe_id);
            $customer->subscriptions->create(array('plan' => 'paid'));

            // update their info in the db
            $user->status = 'paying';
            $user->paid = 'yes';
            $user->expires = null; // in case they reupgrade before their subscription exprires
            $user->save();
        }

        // send confirmation email
        $subject = 'You\'re cleared for takeoff...';
        $body = 'Thank you for upgrading Mailsy to a paid account! You can now send a boatload of emails from Mailsy to increase the size and quality of your prospecting pipeline. As we develop new features for Mailsy, you\'ll get access to them automatically.';

        Utils::sendEmail($user->email,$subject,$body);

        // send them to the settings page so they can see that they're signup for a paid account
        return redirect('/settings?message=upgradeSuccess');
    }

    // create a team and perform the necessary stripe functions
    public function doTeamUpgrade(Request $request)
    {
        $user = Auth::user();

        // return the stripe API key
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

        // validate the domain
        $domain = strstr($user->email,'@');
        $tld = strrpos($domain, '.');
        // strip the tld
        $domain = substr($domain, 0, $tld);
        // strip the @ symbol
        $domain = substr($domain, 1, 50);

        // add them to the customers table
        $customer = new Customer;
        $customer->owner_id = $user->id;
        $customer->company_name = $request->company_name;
        $customer->domain = $domain;
        $customer->total_users = $request->user_count;
        $customer->users_left = $request->user_count;
        $customer->created_at = time();
        $customer->save();

        // update the information in stripe
        // make a new customer if this is their first time upgrading
        if(!$user->stripe_id)
        {
            // Use Stripe's library to make requests...
            $customer = \Stripe\Customer::create(array(
                'source' => $request->stripe_token,
                'plan' => 'paid',
                'email' => $user->email,
                'quantity' => $request->user_count
            ));

            // set their stripe id and their payment settings
            $user->stripe_id = $customer->id;
            $user->status = 'paying';
            $user->admin = 'yes';
            $user->expires = null; // in case they reupgrade before their subscription exprires
            $user->save();
        }
        else
        {
            // return their existing stripe key and handle the 'resignup' if that's the case based on an expiration
            $customer = \Stripe\Customer::retrieve($user->stripe_id);
            $customer->subscriptions->create(array('plan' => 'paid','quantity' => $request->user_count));

            // update their info in the db
            $user->status = 'paying';
            $user->admin = 'yes';
            $user->expires = null; // in case they reupgrade before their subscription exprires
            $user->save();
        }

        // send them a confirmation email
        $subject = 'Mailsy team successfully created';
        $body = 'You\'ve successfully created a team on Mailsy! You have purchased '.$request->user_count.' licenses and your team can signup to use these licenses at '.env('DOMAIN').'/team/'.$domain.'.';

        Utils::sendEmail($user->email,$subject,$body);

        // send them back to the settings page
        return redirect('/settings?message=teamCreated');
    }

    // requests, updates, and return the message status
    public function doUpdateMessageStatus($id)
    {
        // auth the user
        $user = Auth::user();

        $status = Message::updateMessageStatus($id);

        return ucfirst($status);
    }

    // ajax route to return reply rate on home page (so the page doesn't take forever to load since this has to make a call to google for each message)
    public function doReturnReplyRate($email_id)
    {
        // find the messages for this email
        $messageCount = Message::where('email_id',$email_id)->whereNull('deleted_at')->count();

        // return the reply count
        $replyCount = Message::where('email_id',$email_id)->where('status','replied')->whereNull('deleted_at')->count();

        // find the reply percentage
        $replyRate = round(($replyCount / $messageCount) * 100);

        return $replyRate;
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
        $subject = 'Payment method updated for Mailsy';
        // the email body
        $body = 'Your payment method (ending in '.$card->last4.') has been successully added to your account.';

        Utils::sendEmail($user->email,$subject,$body);

        return json_encode($card);
    }

    // update/cancel memberships
    public function doCancelMembership(Request $request)
    {
        // auth the user
        $user = Auth::user();

        // since their an admin cancel their's and everyone they're paying for
        // retrieve the subscription info
        // set the stripe token
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $subscription = $customer->subscriptions->retrieve($customer->subscriptions->data{0}->id);

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

        // if the user has a company team, delete it
        $company = Customer::where('owner_id',$user->id)->whereNull('deleted_at')->first();

        if($company)
        {
            $company->deleted_at = time();
            $company->save();
        }

        // cancel the subscription
        $canceledSubscription = $subscription->cancel(['at_period_end' => true]);
        $user->expires = $subscription->current_period_end;
        $user->has_users = null;
        $user->admin = null; // ditch their admin status
        $user->status = null;
        $user->save();

        // send a confirmation email
        $subject = 'Mailsy Subscription Successfully Canceled';
        $body = 'Your Mailsy subscription has been successfully canceled and use of our paid features will expire on '.date('n/d/Y', $user->expires).'. If you\'d be so kind, please reply to this email and let us know why Mailsy wasn\'t a good fit for you or your team.';

        Utils::sendEmail($user->email,$subject,$body);

        // success message
        return 'true';
    }

    // update/cancel memberships
    public function doUpdateSubscription($direction, Request $request)
    {
        // auth the user
        $user = Auth::user();

        // retrieve the subscription info
        // set the stripe token
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $subscription = $customer->subscriptions->retrieve($customer->subscriptions->data{0}->id);

        // make sure they can't have a quantity that equals zero
        if($request->new_subs == 0)
        {
            return 'cant_be_zero';
        }

        // make sure that they have licenses to deduct
        $company = Customer::where('owner_id',$user->id)->whereNull('deleted_at')->first();

        // for decrementing the subscription quantity...
        if($direction == 'decrease')
        {
            if($company)
            {
                $delta = $company->total_users - $request->new_subs;
                if($delta > $company->users_left)
                {
                    // send back an error if the user messed with the JS reporting on the settings page
                    return 'need_more_free_licenses';
                }
                else
                {
                    // make the update on the mailsy side
                    $company->total_users = $request->new_subs;
                    $company->users_left = $request->new_subs - User::where('belongs_to',$user->id)->whereNull('deleted_at')->count();
                    $company->save();

                    // make the subscription update on the stripe side
                    $subscription->quantity = $request->new_subs;
                    $subscription->save();
                }
            }
            else
            {
                // return an error if they're trying to make an update to something that isn't their company
                return 'wrong_company';
            }
        }
        // if they're adding more licenses....
        elseif($direction == 'increase')
        {
            if($company)
            {
                 // make the update on the mailsy side
                $company->total_users = $request->new_subs;
                $company->users_left = $request->new_subs - User::where('belongs_to',$user->id)->whereNull('deleted_at')->count();
                $company->save();

                // make the subscription update on the stripe side
                $subscription->quantity = $request->new_subs;
                $subscription->save();
            }
            else
            {
                // return an error if they're trying to make an update to something that isn't their company
                return 'wrong_company';
            }
        }

        // email subject
        $subject = 'Mailsy subscription successfully updated';
        // the email body
        $body = 'We\'re writing to let you know that your Mailsy subscription has been successfully updated. If you\'ve reduced your number of licenses, you\'ll get a credit on your next billing cycle for the prorated amount. If you\'ve increased the number of licenses, you\'ll be charged for the prorated amount of these new licenses as part of your next payment.';

        // send the confirmation email
        Utils::sendEmail($user->email,$subject,$body);
    
        return 'success';
    }

    // revoke a user's access but keep their subscription intact
    public function doRevokeAccess(Request $request)
    {
        $user = Auth::user();

        $child = User::find($request->child_id);
        $child->paid = null;
        $child->belongs_to = null;
        $child->save();

        // send the child an email letting them know that they've been revoked
        // send an email to the admin letting them know they're unsubscribed
        $subject = 'Mailsy account downgraded';
        // the email body
        $body = 'We\'re writing to let you know that your paid Mailsy subscription has been downgraded to a free account by '.$user->name.'. If you think this has been done in error, please email your administrator at '.$user->email.'.';
        
        Utils::sendEmail($child->email,$subject,$body);

        // return the company information
        $customer = Customer::where('owner_id',$user->id)->whereNull('deleted_at')->first();

        // if the admin has licenses to get back....
        if($customer->total_users > $customer->users_left)
        {
            $customer->users_left++;
            $customer->save();
        }

        return 'success';
    }

    // add a user to an existing team if there are licenses available
    public function doRedeemLicense(Request $request)
    {
        $user = Auth::user();

        if(User::domainCheck($user->email))
        {
            $company = Customer::find($request->company_id);

            // if they have licenses left
            if($company->users_left > 0)
            {
                // grant the access
                $company->users_left--;
                $user->paid = 'yes';
                $user->belongs_to = $company->owner_id;

                // in case it hasn't been done yet, update the admins 'has_users' status
                User::where('id',$company->owner_id)->update(['has_users' => 'yes']);

                // save everything
                $user->save();
                $company->save();

                return redirect('/settings?message=licenseRedeemed');
            }
            else
            {
                // return an error letting them know that they're out of licenses
                return redirect('/settings?error=noLicenses');
            }
        }
        else
        {
            return redirect('/settings?error=wrongCompany');
        }
    }

    // send feedback on 500 page
    public function doSendFeedback(Request $request)
    {
        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
        $data = array( 
            "to" => array("dave@lucolo.com"=>"David Baines"),
            "from" => array('no-reply@mamilsy.co','Mailsy'),
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

    // send the tutorial email to the user
    public function deleteMessage($id)
    {
        $user = Auth::user();
        $task = Task::findOrFail($id);
        $task->delete();
        Session::flash('flash_message', 'Task successfully deleted!');
        return redirect()->route('tasks.index');
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
                date_default_timezone_set($user->timezone);

                // send a notification email
                $subject = $message->recipient.' opened your Mailsy email!';
                $body = 'We\'re writing to let you know that '.$message->recipient.' opened your email on '.date('D, M d, Y', $message->read_at).' at '.date('g:ia',$message->read_at).' EST.';

                Utils::sendEmail($user->email,$subject,$body);
            }
        }

        $response = Response::make(File::get("images/email-tracker.png"));
        $response->header('Content-Type', 'image/png');
        return $response;
    }

    public function doArchiveTemplate($eid)
    {
        $user = Auth::user();

        // decrypt the id
        $id = base64_decode($eid);

        $email = Email::find($id);
        $email->deleted_at = time();
        $email->save();

        return redirect('/home');
    }

    public function doDearchiveTemplate($eid)
    {
        $user = Auth::user();

        // decrypt the id
        $id = base64_decode($eid);

        $email = Email::find($id);
        $email->deleted_at = null;
        $email->save();

        return redirect('/home');
    }

    public function copyTemplate(Request $request)
    {
        $user = Auth::user();

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
                $fields = array_unique($fields, SORT_REGULAR);
            }
        }
        
        // save the email template
        $email = new Email;
        $email->user_id = $user->id;
        $email->name = $request->_name;
        $email->subject = $request->_subject;
        $email->template = $request->_email_template;
        $email->fields = json_encode($fields);
        $email->created_at = time();
        $email->creator_name = Email::find($request->_email_id)->name;
        $email->shared = 0;
        $email->copies = 0;
        if($user->admin)
        {
            $email->creator_company = $user->id;
        } else
        {
            $email->creator_company = $user->belongs_to;
        }
        $email->save();

        Email::find($request->_email_id)->copies++;
        Email::find($request->_email_id)->save();

        // send the user to the 'use' view
        return redirect('/home');
    }

    public function doHubifyTemplate($eid, $status)
    {
        $user = Auth::user();

        if(intval($status) != 1 && intval($status) != 2 && intval($status) != 0)
        {
            return redirect('/home');
        } else
        {
            $email = User::verifyUser($eid);
            $email->shared = intval($status);
            $email->save();

            // send the user to the 'use' view
            return redirect('/home');
        }

    }

}