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

        return view('pages.home', ['user' => $user, 'emails' => $emails]);
    }

    // for the first time user, show them a tutorial page
    public function showTutorial1()
    {
        $user = Auth::user();

        // update the DB to show that they saw the tutorial
        $user->saw_tutorial_one = 'yes';
        $user->save();

        return view('pages.tutorial1', ['user' => $user]);
    }

    // for the first time user, show them a tutorial page
    public function showTutorial2()
    {
        $user = Auth::user();

        // update the DB to show that they saw the tutorial
        $user->saw_tutorial_two = 'yes';
        $user->save();

        return view('pages.tutorial2', ['user' => $user]);
    }

    // for the first time user, show them a tutorial page
    public function showTutorial3()
    {
        $user = Auth::user();

        // update the DB to show that they saw the tutorial
        $user->saw_tutorial_three = 'yes';
        $user->save();

        return view('pages.tutorial3', ['user' => $user]);
    }

    // the email creation page
    public function showNewEmail()
    {
        $user = Auth::user();
        // count the emails that this user has
        $emails = Email::where('user_id',$user->id)->whereNull('deleted_at')->count();
        return view('pages.create', ['user' => $user, 'emails' => $emails]);
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

        // if there are no message to show, reidrect them back to the /email/ view
        if($messages == '[]')
        {
            return redirect('/email/'.base64_encode($email->id));
        }

        // if all is good to go, return the view with the previews
        return view('pages.preview', ['user' => $user, 'email' => $email, 'messages' => $messages]);
    }

    // show an edit page for the email that has been created
    public function showEdit($eid, $withData = NULL)
    {
        $user = Auth::user();

        $email = User::verifyUser($eid);

        // if there should be previous messages shown, keep them in the 'temp_recipients_list' field. if not, make sure there's nothing there
        if(!$withData)
        {
            $email->temp_recipients_list = null;
            $email->save();
        }

        // if you're editing a template, erase the messages that haven't been sent
        Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->update(['deleted_at' => time()]);
        
        return view('pages.edit', ['email' => $email, 'user' => $user]);

    }

    // allow the user to use their email templates
    public function showUseEmail($eid)
    {
        $user = Auth::user();

        $email = Email::find(base64_decode($eid));

        // if there are messages that are 'in the queue', make sure they're deleted as the user is about to enter more
        Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->update(['deleted_at' => time()]);

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

        // grab the card info if it's a paid user that's currently paying
        if($user->stripe_id)
        {
            \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

            $stripeUser = \Stripe\Customer::retrieve($user->stripe_id);
            $user->lastFour = $stripeUser->sources->data{0}->last4;
            $user->exp = $stripeUser->sources->data{0}->exp_month.'/'.$stripeUser->sources->data{0}->exp_year;
            $user->state = $stripeUser->sources->data{0}->deliquent;
            $user->nextDue = $stripeUser->subscriptions->data{0}->current_period_end;
        }

        // if the user has paid for other users
        if($user->has_users)
        {
            // get the users that this user has paid for
            $children = User::where('belongs_to',$user->id)->whereNull('deleted_at')->get();
        }
        else
        {
            $children = null;
        }

        // fetch their admin details if they are a part of one
        // get the domain name for the url that we'll create
        $domain = strstr($user->email,'@');
        $tld = strrpos($domain, '.');
        // strip the tld
        $domain = substr($domain, 0, $tld);
        // strip the @ symbol
        $domain = substr($domain, 1, 50);

        // return all the company info if they're the admin
        $customerDetails = Customer::where('owner_id', $user->id)->whereNull('deleted_at')->first();

        // return just basic info if they're a part of the company but not the admin
        $company = Customer::where('domain',$domain)->whereNull('deleted_at')->first();

        $company->admin = User::where('id',$company->owner_id)->first();

        $company->email = $company->admin->email;

        // parse the view
        return view('pages.settings', ['user' => $user, 'children' => $children, 'customer_details' => $customerDetails, 'company' => $company]);
    }

    // show the upgrade page
    public function showUpgrade()
    {
        $user = Auth::user();

        if($user->paid || ($user->status == 'paying'))
        {
            return redirect('/settings');
        }

        return view('pages.upgrade', ['user' => $user]);
    }

    // show a confirmation page regarding user management
    public function showCancel()
    {
        // auth the user
        $user = Auth::user();

        // get the subscription info
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $subscription = $customer->subscriptions->retrieve($customer->subscriptions->data{0}->id);

        return view('pages.confirm', ['user' => $user, 'end_date' => $subscription->current_period_end]);
    }

    // page to add users
    public function showCreateTeam()
    {
        // auth the user
        $user = Auth::user();

        // get the domain name for the url that we'll create
        $domain = strstr($user->email,'@');
        $tld = strrpos($domain, '.');
        // strip the tld
        $domain = substr($domain, 0, $tld);
        // strip the @ symbol
        $domain = substr($domain, 1, 50);

        return view('pages.createTeam',['user' => $user, 'domain' => $domain]);
    }
}
