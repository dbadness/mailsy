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
use App\Event;
use Log;
use View;

class PagesController extends Controller
{
    public function __construct()
    {
        //Use this to set any variables that should be available to all functions in this controller.
        $this->user = Auth::user();

        //Use this to share any variables that should be available to all pages. This includes anything needed in the header or footer.
        View::share('user', $this->user);
    }

    // show the home page once the user is authed
    public function showTemplates()
    {
        //return their emails and it's metadata if not archived
        $emails = Email::where('user_id',$this->user->id)->whereNull('one_off')->whereNull('deleted_at')->paginate(9);

        // if there are emails to show, update their message statuses so the reply rates are accurate
        if($emails != '[]')
        {
            foreach($emails as $email)
            {
                $messages = Message::where('email_id',$email->id)->whereNull('deleted_at')->get();

                if($messages != '[]')
                {
                    foreach($messages as $message)
                    {
                        Message::updateMessageStatus($message->id);
                    }
                }
            }
        }

        //return their emails and it's metadata if not archived
        $archived = Email::where('user_id',$this->user->id)->whereNotNull('deleted_at')->count();

        return view('pages.templates', ['emails' => $emails, 'archived' => $archived]);
    }

    // if this is a new non-google user, send them to the smtp set up page
    public function showSmtpSetup()
    {
        return view('pages.smtp-setup');

    }

    // for the first time user, show them a tutorial page
    public function showTutorial1()
    {
        // update the DB to show that they saw the tutorial
        $this->user->saw_tutorial_one = 'yes';
        $this->user->save();

        return view('pages.tutorial1');
    }

    // for the first time user, show them a tutorial page
    public function showTutorial2()
    {
        // update the DB to show that they saw the tutorial
        $this->user->saw_tutorial_two = 'yes';
        $this->user->save();

        return view('pages.tutorial2');
    }

    // for the first time user, show them a tutorial page
    public function showTutorial3()
    {
        // update the DB to show that they saw the tutorial
        $this->user->saw_tutorial_three = 'yes';
        $this->user->save();

        return view('pages.tutorial3');
    }

    // the email creation page
    public function showNewEmail()
    {
        // count the emails that this user has
        $emails = Email::where('user_id',$this->user->id)->whereNull('deleted_at')->count();

        return view('pages.create', ['emails' => $emails]);
    }

    // show the email preview page
    public function showPreview($eid)
    {
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
        return view('pages.preview', ['email' => $email, 'messages' => $messages]);
    }

    // show an edit page for the email that has been created
    public function showEdit($eid, $withData = NULL)
    {
        $email = User::verifyUser($eid);

        // if there should be previous messages shown, keep them in the 'temp_recipients_list' field. if not, make sure there's nothing there
        if(!$withData)
        {
            $email->temp_recipients_list = null;
            $email->save();
        }

        // if you're editing a template, erase the messages that haven't been sent
        Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->update(['deleted_at' => time()]);
        
        return view('pages.edit', ['email' => $email]);

    }

    // allow the user to use their email templates
    public function showUseEmail($eid)
    {
        $email = User::verifyUser($eid);

        // if there are messages that are 'in the queue', make sure they're deleted as the user is about to enter more
        Message::where('email_id',$email->id)->whereNull('deleted_at')->whereNull('status')->update(['deleted_at' => time()]);

        return view('pages.use', ['email' => $email]);
    }

    // show the messages for an email
    public function showEmail($eid)
    {
        // make sure this email belongs to this user
        $email = User::verifyUser($eid);

        // go through the messages and set the statuses of the messages
        $messages = Message::where('email_id',$email->id)->whereNotNull('status')->whereNull('deleted_at')->get();

        return view('pages.email', ['email' => $email, 'messages' => $messages]);
    }

    // the settings page
    public function showSettings()
    {
        // grab the card info if it's a paid user that's currently paying
        if($this->user->stripe_id)
        {
            \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));

            $stripeUser = \Stripe\Customer::retrieve($this->user->stripe_id);
            $this->user->lastFour = $stripeUser->sources->data{0}->last4;
            $this->user->exp = $stripeUser->sources->data{0}->exp_month.'/'.$stripeUser->sources->data{0}->exp_year;
            $this->user->state = $stripeUser->sources->data{0}->deliquent;
            $this->user->nextDue = $stripeUser->subscriptions->data{0}->current_period_end;
        }

        // if the user has paid for other users
        if($this->user->has_users)
        {
            // get the users that this user has paid for
            $children = User::where('belongs_to',$this->user->id)->whereNull('deleted_at')->get();
        }
        else
        {
            $children = null;
        }

        // return the company info
        $company = User::domainCheck($this->user->email);

        if($company)
        {
            $company->admin = User::where('id',$company->owner_id)->first();

            $company->email = $company->admin->email;
        }

        // parse the view
        return view('pages.settings', ['children' => $children, 'company' => $company]);
    }

    // show the upgrade page
    public function showUpgrade()
    {
        if($this->user->paid || ($this->user->status == 'paying'))
        {
            return redirect('/settings');
        }

        return view('pages.upgrade');
    }

    // show a confirmation page regarding user management
    public function showCancel()
    {
        // get the subscription info
        \Stripe\Stripe::setApiKey(env('STRIPE_TOKEN'));
        $customer = \Stripe\Customer::retrieve($this->user->stripe_id);
        $subscription = $customer->subscriptions->retrieve($customer->subscriptions->data{0}->id);

        return view('pages.confirm', ['end_date' => $subscription->current_period_end]);
    }

    // page to add users
    public function showCreateTeam()
    {
        // get the domain name for the url that we'll create
        $domain = strstr($this->user->email,'@');
        $tld = strrpos($domain, '.');
        // strip the tld
        $domain = substr($domain, 0, $tld);
        // strip the @ symbol
        $domain = substr($domain, 1, 50);

        return view('pages.createTeam',['domain' => $domain]);
    }

    // show archived templates
    public function showArchive()
    {
        //return their emails and it's metadata if archived
        $emails = Email::where('user_id',$this->user->id)->whereNotNull('deleted_at')->paginate(9);

        return view('pages.archives', ['emails' => $emails]);
    }

    // show an edit page for the email that has been created
    public function showCopy($eid)
    {
        $email = User::verifyUser($eid);
        
        return view('pages.copy', ['email' => $email]);
    }

    // show an edit page for the email that has been created
    public function showView($eid)
    {
        $email = User::verifyUser($eid);
        
        return view('pages.view', ['email' => $email]);
    }

    // show the template hub
    public function showPublicTemplates()
    {
        if(!$this->user->paid)
        {
            return redirect('/home');
        }

        //return the emails that have been marked for the hub
        $emails = Email::where('shared',2)->paginate(9);

        return view('pages.publictemplates', ['emails' => $emails]);
    }

    // show the template hub
    public function showPrivateTemplates()
    {
        if(!$this->user->paid)
        {
            return redirect('/home');
        }

        //return the emails that have been marked for the hub
        if($this->user->admin)
        {
            $emails = Email::where('shared',1)->where('creator_company',$this->user->id)->paginate(9);
        } else
        {
            $emails = Email::where('shared',1)->where('creator_company',$this->user->belongs_to)->paginate(9);
        }

        return view('pages.privatetemplates', ['emails' => $emails]);
    }

    // show an edit page for the email that has been created
    public function showAdmin()
    {
        if($this->user->admin != "yes"){
            return redirect('/settings');
        }

        // return the company info
        $company = User::domainCheck($this->user->email);

        if($company)
        {
            $company->admin = User::where('id',$company->owner_id)->first();

            $company->email = $company->admin->email;
        }

        $children = User::where('belongs_to',$company->owner_id)->whereNull('deleted_at')->get();
        $teams = User::where('belongs_to',$company->owner_id)->whereNull('deleted_at')->whereNotNull('team_admin')->get();
        $members = User::where('belongs_to_team', $this->user->id)->get();

        return view('pages.admin', ['company' => $company, 'children' => $children, 'teams' => $teams, 'members' => $members]);
    }

    // show ephemeral template page
    public function showSend()
    {

        return view('pages.ephemeral', []);
    }

    // show ephemeral template page
    public function showOutbox()
    {

        $messages = Message::where('user_id',$this->user->id)->orderBy('created_at', 'desc')->paginate(20);

        return view('pages.outbox', ['messages' => $messages]);
    }

    // show ephemeral template page
    public function showHome()
    {

        $events = Event::where('user_id', $this->user->id)->where('timestamp', '>', $this->user->second_last_login)->orderBy('timestamp', 'desc')->paginate(100);

        return view('pages.home', ['events' => $events]);
    }

    // show ephemeral template page
    public function showSendOne($feedback = null)
    {

        return view('pages.sendOne', ['feedback' => $feedback]);
    }

    // show ephemeral template page
    public function showEvents()
    {

        $events = Event::where('user_id', $this->user->id)->orderBy('timestamp', 'desc')->paginate(20);

        return view('pages.events', ['events' => $events]);
    }

    // show nav/stie tutorial
    public function showSiteTut()
    {

        return view('pages.sitetut', []);
    }
}
