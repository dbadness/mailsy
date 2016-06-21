<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Auth;
use App\Message;
use Log;

// for Sendinblue
use \Sendinblue\Mailin as Mailin;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $visible = ['id','email'];

    // allow the app to fill the fields in the DB
    protected $fillable = ['sf_address', 'signature'];

    // don't automatically add timestamps to new/updated records
    public $timestamps = false;

    public static function makeNewUser()
    {
        //
    }

    /** filter the page for this specific user.
     *
     * @param $email id str email of new user
     * @param $name str user's name (if given from google)
     * @param $password str user's password (if they're not using google)
     * @param $referer str refering website preceding signup
     * @param $googleToken str google token if it's a google signup
     * @param $license bool flag if the new user is using a license to signup
     * @return $email Object the id of the email object
     */
    public static function createUser($email, $password = null, $name, $referer, $googleToken = null, $license = null)
    {
        // create a new user
        $user = new User;

        $user->email = $email;
        $user->password = $password;
        $user->name = $name;
        $user->gmail_token = $googleToken;
        // set a flag on their user type
        if($googleToken)
        {
            $user->gmail_user = 1;
        }
        else
        {
            $user->gmail_user = 0;
        }
        $user->created_at = time();
        $user->track_email = 'yes';
        $user->track_links = 'yes';
        $user->timezone = 'America/New_York';
        $user->referer = $referer;

        // check if they're using up a license for this signup
        $company = User::domainCheck($email);
        if($company && $license)
        {
            // make sure there's a company license to use...
            if($company->users_left > 0)
            {
                // update the user to paid and save the new decremented user count
                $user->paid = 'yes';
                $user->belongs_to = $company->owner_id;
                $company->users_left--;
                $company->save();
            }
            else
            {
                // sign them up as a free user and let them and the admin know that they need more licenses to sign folks up
                // find the company admin
                $admin = User::find($company->owner_id);

                // send the user an email
                $subject = 'There are no more licenses for '.$company->company_name;
                $body = 'You just tried to signup for a paid Mailsy account through the '.$company->company_name.' team. Unfortunately there ';
                $body .= 'are no more available licenses on that account. Please email the administrator ('.$admin->email.') and let them know ';
                $body .= 'that you need a license. Until then, you have access to Mailsy on a free account.';

                // send the email
                Utils::sendEmail($user->email,$subject,$body);

                // send the admin an email
                $subject = 'There are no more licenses for '.$company->company_name;
                $body = 'Someone ('.$user->email.') just tried to signup for a paid Mailsy account through your '.$company->company_name.' team. Unfortunately there ';
                $body .= 'are no more available licenses on that account. Please log into Mailsy, add more licenses, and let that user ';
                $body .= 'know that they can try again to use a license. They\'ve been signed up as a free user so ';
                $body .= 'they can just \'join your team\' when you\'re ready.';

                // send the email
                Utils::sendEmail($admin->email,$subject,$body);
            }
        }

        // add them to the marketing database
        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
        $data = array(
          "email" => $user->email,
          "listid" => array(2)
        );
        $mailin->create_update_user($data);

        // save it to the DB
        $user->save();

        // return the user object
        return $user;
    }


    /** filter the page for this specific user.
     *
     * @param $eid id str encrypted email id
     * @param $user_id int auth'd users id
     * @return $email Object the id of the email object
     */
    public static function verifyUser($eid)
    {
        $id = base64_decode($eid);
        $email = Email::find($id);
        // make sure the user is the author of the email
        $user = Auth::user();
        if($email->user_id != $user->id)
        {
            return abort(403);
        }
        else
        {
            return $email;
        }
    }

    // build the Google Client for API calls
    public static function googleClient() {

        $user = Auth::user();

        $client = new \Google_Client();
        $client->setApplicationName(env('GOOGLE_APP_NAME'));
        $client->setDeveloperKey(env('GOOGLE_KEY'));
        $client->setClientID(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setAccessToken($user->gmail_token);

        // Refresh the token if it's expired.
        if($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            $newToken = $client->getAccessToken();
            $user->gmail_token = $newToken;
            $user->save();
        }

        return $client;
    }

    // find out how many emails this user has left today if their not a paid member
    public static function howManyEmailsLeft()
    {
        // auth the user
        $user = Auth::user();

        // set the timezone
        date_default_timezone_set($user->timezone);

        // initate the count
        $left = 10;

        $last = strtotime('today');
        $next = strtotime('today') + (60*60*24) - 1;

        // retrieve the messages from today
        $messages = Message::where('user_id',$user->id)->whereNotNull('status')->whereNull('deleted_at')->whereBetween('sent_at',[$last,$next])->get();

        if($messages && !$user->paid)
        {
            $left = $left - count($messages);
        }

        return $left;
    }

    // check if this user is a part of the domain that's in the system
    public static function domainCheck($email)
    {
        // get the domain name for the url that we'll create
        $domain = strstr($email,'@');
        $tld = strrpos($domain, '.');
        // strip the tld
        $domain = substr($domain, 0, $tld);
        // strip the @ symbol
        $domain = substr($domain, 1, 50);

        // return just basic info if they're a part of the company but not the admin
        $companyDetails = Customer::where('domain',$domain)->whereNull('deleted_at')->first();

        if($companyDetails)
        {
            return $companyDetails;
        }
        else
        {
            return false;
        }
    }
}