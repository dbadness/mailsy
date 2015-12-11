<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\User;
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
        // auth the user
        $user = Auth::user();

        //return their emails and it's metadata
        $emails = Email::where('user_id',$user->id)->get();

        // set up the data array for the view
        $data = ['user' => $user, 'emails' => $emails];

        return view('pages.home', ['data' => $data]);
    }

    // the email creation page
    public function showNewEmail()
    {
        $user = Auth::user();
        return view('pages.create', ['user' => $user]);
    }

    // show the email preview page
    public function showPreview($eid)
    {
        // auth the user
        $user = Auth::user();

        // decode the email
        $email = User::verifyUser($eid);
        // retrieve the messages that aren't deleted or sent for this email
        $messages = Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->get();

        // if all is good to go, return the view with the previews
        return view('pages.preview', ['user' => $user, 'email' => $email, 'messages' => $messages]);
    }

    // show an edit page for the email that has been created
    public function showEdit($eid)
    {
        $user = Auth::user();

        $email = User::verifyUser($eid);

        // if you're editing a template, erase the messages that haven't been sent
        Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->update(['deleted_at' => time()]);
        
        return view('pages.edit', ['email' => $email, 'user' => $user]);

    }

    // allow the user to use their email templates
    public function showUseEmail($eid)
    {
        $user = Auth::user();
        $email = Email::find(base64_decode($eid));

        return view('pages.use', ['user' => $user, 'email' => $email]);
    }

    // show the messages for an email
    public function showEmail($eid)
    {
        $email = User::verifyUser($eid);

        // go through the messages and set the statuses of the messages
        $messages = Message::where('email_id',$email->id)->whereNull('deleted_at')->get();

        return view('pages.email', ['email' => $email, 'messages' => $messages]);
    }

    // the settings page
    public function showSettings()
    {
        $user = Auth::user();
        if($user->has_users)
        {
            // get the users that this user has paid for
            $children = User::where('belongs_to',$user->id)->whereNull('paid')->whereNull('deleted_at')->get();
        }
        else
        {
            $children = null;
        }
        return view('pages.settings', ['user' => $user, 'children' => $children]);
    }

    // show the upgrade page
    public function showUpgrade()
    {
        $user = Auth::user();
        return view('pages.upgrade', ['user' => $user]);
    }
}
