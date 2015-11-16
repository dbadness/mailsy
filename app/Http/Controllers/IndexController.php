<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Auth;

class IndexController extends Controller
{
    // show the home page
    public function showIndex()
    {
        return view('pages.index');
    }

    // show the signup page
    public function showSignup()
    {
        return view('pages.signup');
    }

    // send the user through oauth2 process for the Gmail API
    public function sendToGoogleAuth()
    {
        $client = new \Google_Client();
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setRedirectURI('http://newco.dev/signup');        
        $client->setScopes('https://www.googleapis.com/auth/gmail.modify');
        $client->setAccessType('offline');

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
        $client->setRedirectURI('http://newco.dev/signup');
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // $accessToken = $client->authenticate(urldecode($request->code));

        $accessToken = $client->authenticate($_GET['code']);

        $client->setAccessToken($accessToken);

        $service = new \Google_Service_Gmail($client);

        $gmailUser = $service->users->getProfile('me');

        // don't let them sign up twice
        $existingUser = User::where('email',$gmailUser->emailAddress)->first();

        // if this is a duplicate, just sign them in (don't sign them up again)
        if($existingUser != '[]')
        {
            // log the user in and send them to the home page
            $success = Auth::loginUsingId($existingUser->id);
            return redirect('/home');
        }
        else
        {
            // create a new user
            $user = new User;

            $user->email = $gmailUser->emailAddress;
            $user->gmail_token = $accessToken;
            $user->created_at = time();

            // save it to the DB
            $user->save();

            // now logthe user in
            $user = Auth::loginUsingId($user->id);

            // send them to their dashboard
            return redirect('/home');
        }
    }
}
