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

    // don't automitically add timestamps to new/updated records
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
}