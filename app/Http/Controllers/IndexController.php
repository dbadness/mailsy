<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Customer;
use App\Utils;
use Auth;

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

        return view('layouts.index');
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
    public function doAuth($license = null)
    {
        $client = new \Google_Client();
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        // send a flag to return to the app so we know to pull a license
        if($license)
        {
            $client->setRedirectURI(env('GOOGLE_URI_REDIRECT').'/license'); 
        }
        else
        {
            $client->setRedirectURI(env('GOOGLE_URI_REDIRECT'));
        }      
        $client->setScopes(['https://www.googleapis.com/auth/gmail.send', 'profile', 'email']);
        $client->setAccessType('offline');
        // $client->setApprovalPrompt('force'); // so we're sure to show the screen to the user (and get a refresh token)

        $url = $client->createAuthUrl();

        return redirect($url);
    }

    // if the gmail auth was sucessful, this adds them to the DB
    public function doAddUser($license = null)
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

        $accessToken = $client->authenticate($_GET['code']);

        $client->setAccessToken($accessToken);

        $googlePlus = new \Google_Service_Plus($client);
        $userProfile = $googlePlus->people->get('me');
        $name = $userProfile->displayName;
        $email = $userProfile->emails{0}->value;
        // don't let them sign up twice
        $existingUser = User::where('email',$email)->first();

        // if this is a duplicate, just sign them in (don't sign them up again)
        if($existingUser)
        {
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
            $existingUser->save();

            // send them home
            return redirect('/home');
        }
        else
        {
            // create a new user
            $user = new User;

            // get the referer and throw them in the DB
            if(isset($_COOKIE['mailsy_referer']))
            {
                $referer = $_COOKIE['mailsy_referer'];
            }
            else
            {
                $referer = 'NA';
            }

            $user->email = $email;
            $user->name = $name;
            $user->gmail_token = $accessToken;
            $user->created_at = time();
            $user->track_email = 'yes';
            $user->timezone = 'America/New_York';
            $user->referer = $referer;

            // check if they're using a license
            $domainDetails = User::domainCheck($email);
            if($domainDetails && $license)
            {
                $user->paid = 'yes';
                $user->belongs_to = $domainDetails->owner_id;
            }

            // save it to the DB
            $user->save();

            // add them to the marketing database
            $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
            $data = array(
              "email" => $user->email,
              "listid" => array(2)
            );
            $mailin->create_update_user($data);

            // now logthe user in
            $user = Auth::loginUsingId($user->id);

            // send them to their dashboard
            return redirect('/tutorial/step1');
        }
    }
}
