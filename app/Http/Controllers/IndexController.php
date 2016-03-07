<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
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
    public function showLogin()
    {
        return view('pages.login');
    }

    public function showFaq()
    {
        return view('layouts.faq');
    }

    // send the user through oauth2 process for the Gmail API
    public function doAuth()
    {
        $client = new \Google_Client();
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectURI(env('GOOGLE_URI_REDIRECT'));        
        $client->setScopes(['https://www.googleapis.com/auth/gmail.send', 'profile', 'email']);
        $client->setAccessType('offline');
        // $client->setApprovalPrompt('force'); // so we're sure to show the screen to the user (and get a refresh token)

        $url = $client->createAuthUrl();

        return redirect($url);
    }

    // if the gmail auth was sucessful, this adds them to the DB
    public function doAddUser()
    {
        // find the user's email in the Google API
        $client = new \Google_Client();
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setRedirectURI(env('GOOGLE_URI_REDIRECT'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

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
            $existingUser->track_email = 'yes';
            $existingUser->name = $name;
            $existingUser->save();
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
            $user->referer = $referer;

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
