<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Email;
use App\Message;

class PagesController extends Controller
{
    public function __construct()
    {
        // everything in this controller is for authed users only
        $this->middleware('auth');
    }

    // show the home page once the user is authed
    public function showHome()
    {
        $user = Auth::user();
        return view('pages.home', ['user' => $user]);
    }

    // the email creation page
    public function showNewEmail()
    {
        $user = Auth::user();
        return view('pages.newemail', ['user' => $user]);
    }

    // show the email preview page
    public function showPreview($encyptedEmail)
    {
        $id = base64_decode($encyptedEmail);
        $email = Email::where('id',$id)->get();
        // make sure the user is the auther of the email
        $user = Auth::user();
        if($email->id != $user->id)
        {
            return abort(403);
            die;
        }

        // retrieve the messages
        $messages = Message::where('email_id',$email->id)->get();

        // if all is good to go, return the view with the previews
        return view('pages.preview', ['email' => $email, 'messages' => $messages]);
    }

    // show an edit page for the email that has been created
    public function showEdit($encyptedEmail)
    {
        $id = base64_decode($encyptedEmail);
        $email = Email::where('id',$id)->get();
        // make sure the user is the auther of the email
        $user = Auth::user();
        if($email->id != $user->id)
        {
            return abort(403);
            die;
        }

        return view('pages.edit', ['email' => $email]);

    }
}
