<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Customer;
use App\Message;
use App\Utils;
use App\Event;
use Auth;
use Log;
use Response;
use File;

use \Sendinblue\Mailin as Mailin;

class IndexController extends Controller
{
    // show the home page
    public function showIndex()
    {
        // set cookie to track signup referals
        if(isset($_SERVER['HTTP_REFERER']))
        {
            setcookie('mailsy_referer', $_SERVER['HTTP_REFERER'], time() + (86400 * 30), '/'); // 86400 = 1 day
        }

        $user = Auth::user();

        return view('pages.index', ['user' => $user]);
    }

    // show the signup page
    public function showSignup($license = null, $companyDomain = null)
    {
        $user = Auth::user();

        if($companyDomain)
        {
            $company = Customer::where('domain',$companyDomain)->whereNull('deleted_at')->first();
        } else
        {
            $company = null;
        }

        return view('pages.signup',['user' => $user, 'company' => $company]);
    }

    // show the signup page
    public function showLogin()
    {
        $user = Auth::user();

        return view('pages.login',['user' => $user]);
    }

    // display a login page
    public function showCompanyPage($customer_url)
    {
        $customer = Customer::where('domain',$customer_url)->whereNull('deleted_at')->first();

        if($customer)
        {
            return view('pages.customer', ['customer' => $customer]);
        }
        else
        {
            return redirect('/');
        }
    }

    public function showFaq()
    {
        return view('layouts.faq');
    }

    // send the user through oauth2 process for the Gmail API
    // @param $signup bool forces the google approval page to get the refresh token
    // @param $license bool if this user is using a license to signup
    public function doAuth($signup, $license)
    {
        $client = new \Google_Client();
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        // send a flag to return to the app so we know to pull a license
        if($license == 1)
        {
            $client->setRedirectURI(env('GOOGLE_URI_REDIRECT').'/license'); 
        }
        else
        {
            $client->setRedirectURI(env('GOOGLE_URI_REDIRECT'));
        }      
        $client->setScopes(['https://mail.google.com', 'profile', 'email']);
        $client->setAccessType('offline');

        // if they're signing up for the first time force the prompt so we can get a refresh token
        if($signup == 1)
        {
            $client->setApprovalPrompt('force'); // so we're sure to show the screen to the user (and get a refresh token)
        }
        
        $url = $client->createAuthUrl();

        return redirect($url);
    }

    // if the gmail auth was successful, this adds them to the DB
    public function doAddGmailUser($license = null)
    {
        // find the user's email in the Google API
        $client = new \Google_Client();
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // make sure we accomodate the lincense flag if it's there
        if($license)
        {
            $client->setRedirectURI(env('GOOGLE_URI_REDIRECT').'/license'); 
        }
        else
        {
            $client->setRedirectURI(env('GOOGLE_URI_REDIRECT'));
        }

        if(isset($_GET['code']))
        {
            $accessToken = $client->authenticate($_GET['code']);
        } else
        {
            return redirect('/signup');
        }

        $client->setAccessToken($accessToken);

        // return the google user's name and email for our DB
        $googlePlus = new \Google_Service_Plus($client);
        $userProfile = $googlePlus->people->get('me');
        $name = $userProfile->displayName;
        $email = $userProfile->emails{0}->value;

        // don't let them sign up twice
        $existingUser = User::where('email',$email)->first();

        $processedAccessToken = json_decode($accessToken, true);
        $refreshExists = preg_match('/refresh_token/', $accessToken);

        // if this is a duplicate, just sign them in (don't sign them up again)
        if($existingUser && $refreshExists)
        {
            return redirect('/login?error=accountExists');
        }
        elseif(!$existingUser && !$refreshExists)
        {
            return redirect('/signup?error=accountDNE');
        }
        elseif($existingUser && !$refreshExists)
        {
            //need to handle refresh token better
            // $processedAcessToken["refresh_token"] = $existingUser->refresh_token;

            // log the user in and send them to the home page
            $success = Auth::loginUsingId($existingUser->id);

            // update the user's google_token
            $existingUser->gmail_token = $accessToken;
            // update their status if they have an expiration date
            if($existingUser->expires)
            {
                if($existingUser->expires < time())
                {
                    $existingUser->paid = null;
                }
            }

            // add the timezone if there isn't one in the DB yet
            if(!$existingUser->timezone)
            {
                $existingUser->timezone = 'America/New_York';
            }

            $existingUser->name = $name;
            $existingUser->last_login = time();
            $existingUser->second_last_login = $existingUser->last_login;
            $existingUser->gmail_user = 1;
            $existingUser->save();

            // $processedAccessToken["refresh_token"] = $existingUser->refresh_token;
            // $client->setAccessToken(json_encode($processedAccessToken));
            // $client->refreshToken($existingUser->refresh_token);

            // send them to the dashboard
            return redirect('/sendone');
        }
        elseif(!$existingUser && $refreshExists)
        {
            // make a new user and return that object
            // get the referer and throw them in the DB
            if(isset($_COOKIE['mailsy_referer']))
            {
                $referer = $_COOKIE['mailsy_referer'];
            }
            else
            {
                $referer = 'NA';
            }

            // write the user the the DB (minus the PW since they don't need one)
            $user = User::createUser($email, null, $name, $referer, $accessToken, $license);

            // now log the user in
            $user = Auth::loginUsingId($user->id);
            $user->refresh_token = $processedAccessToken["refresh_token"];
            $user->gmail_user = 1;
            $user->save();

            // send them to their dashboard
            return redirect('/tutorial/step1');
        }
    }

    // if this isn't a google signup, create the user manually
    public function doSignup(Request $request, $license = null)
    {
        // make sure they're not signing up twice
        $existingUser = User::where('email',$request->email)->first();
        if($existingUser)
        {
            // redirect to the log in page
            return redirect('/login');
        }
        else
        {
            // make a new user and return that object
            // set the variables and write the user to the DB
            // get the referer and throw them in the DB
            if(isset($_COOKIE['mailsy_referer']))
            {
                $referer = $_COOKIE['mailsy_referer'];
            }
            else
            {
                $referer = 'NA';
            }
            $password = sha1($request->password);
            $user = User::createUser($request->email, $password, $request->name, $referer, null, $license);

            // log them in and send them to the smtp set up page
            $user = Auth::loginUsingId($user->id);
            return redirect('/smtp-setup');
        }
    }

    // authenticate the user with an email and password
    public function doLogin(Request $request)
    {

        $user = User::where('email',$request->email)->where('password',sha1($request->password))->first();

        if($user)
        {
            Auth::loginUsingId($user['id']);
            // save their 'last_login' value
            $user->last_login = time();
            $user->save();

            // send them to their dashboard
            return redirect('/home');
        }
        else
        {
            return redirect('/login?email='.$request->email);
        }
    }

    // for testing an agnostic smtp system
    public function showSmtpTester()
    {
        return view('testing.smtp-tester');
    }

    // send a test email when the user sets up their smtp settings
    public function doSmtpTester(Request $request)
    {
        // Create the Transport
        try
        {

            if(Auth::user()){
                $user = Auth::user();
                $to = $user->email;
                $from = $user->email;
            } else{
                $to = $request->_to;
                $from = $request->_from;
            }

            $transport = \Swift_SmtpTransport::newInstance($request->smtp_server, $request->smtp_port, $request->smtp_protocol)->setUsername($request->smtp_uname)->setPassword($request->smtp_password);

            $mailer = \Swift_Mailer::newInstance($transport);

            $mail = new \Swift_Message;

            // Create a message
            $subject = 'Test email from Mailsy.';

            $body = 'Hi there,<br><br>Looks like your SMTP server is compatible! If you\'ve already signed up, you can now <a href="'.env('DOMAIN').'/smtp-setup">save your email settings on Mailsy</a> and start sending out emails en masse!<br><br>- The Mailsy Team';

            $mail->setFrom(array($from));
            $mail->setTo([$to => $to]);
            $mail->setBody($body, 'text/html');
            $mail->setSubject($subject);
            // $mail->attach(\Swift_Attachment::fromPath('my-document.pdf'));

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

    // webhook for emails opened by the recipients (read receipts) and returns an image to fool the email
    // we'll also need the user id since this webhook is stateless
    public function doTrack($e_user_id, $e_message_id)
    {

        // decrypt the ids
        $user_id = base64_decode(quoted_printable_decode($e_user_id));
        $message_id = base64_decode(quoted_printable_decode($e_message_id));

        // get the message id and make the DB update
        $message = Message::find($message_id);

        $event = new Event;
        $event->user_id = $user_id;
        $event->message_id = $message_id;
        $event->event_type = "message_open";
        $event->timestamp = time();
        $event->event_message = $message->subject." opened";
        $event->save();

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
                $subject = 'Mailsy email '.$message->subject.' opened!';
                $body = 'We\'re writing to let you know that someone opened your email '. $message->subject .' on '.date('D, M d, Y', $message->read_at).' at '.date('g:ia',$message->read_at).' EST.';

                Utils::sendEmail($user->email,$subject,$body);
            }
        }

        $response = Response::make(File::get("images/email-tracker.png"));
        $response->header('Content-Type', 'image/png');
        return $response;
    }


    // webhook for links clicked by the recipients (read receipts) and returns an image to fool the email
    // we'll also need the user id since this webhook is stateless and the redirect to make the link work
    public function doTrackLink($e_user_id, $e_message_id, $e_redirect)
    {
        // decrypt the ids
        $user_id = base64_decode($e_user_id);
        $message_id = base64_decode($e_message_id);
        $e_redirect = base64_decode($e_redirect);

        // get the message id and make the DB update
        $message = Message::find($message_id);

        $user = Auth::loginUsingId($user_id);
        $event = new Event;
        $event->user_id = $user_id;
        $event->message_id = $message_id;
        $event->event_type = "link_open";
        $event->timestamp = time();
        $event->event_message = "Someone clicked through the link to ".$e_redirect." in the email ".$message->subject;
        $event->save();

        if($user->track_links = 'yes')
        {
                date_default_timezone_set($user->timezone);

                // send a notification email
                $subject = 'Mailsy email link clicked through!!';
                $body = 'We\'re writing to let you know that someone opened the link '. $e_redirect .' on '.date('D, M d, Y', $event->timestamp).' at '.date('g:ia',$event->timestamp).' EST.';

                Utils::sendEmail($user->email,$subject,$body);
        }

        return redirect($e_redirect);
    }

}
