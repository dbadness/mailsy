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
    protected $visible = ['id','email'];

    // allow the app to fill the fields in the DB
    protected $fillable = ['sf_address', 'signature'];

    // don't automatically add timestamps to new/updated records
    public $timestamps = false;

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
        // in EST
        date_default_timezone_set('EST');
        // auth the user
        $user = Auth::user();

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

    // check if this user is a part of the domain that's logged in
    public static function domainCheck()
    {
        $user = Auth::user();

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

        if($customerDetails)
        {
            return true;
        }
        elseif($company)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}