<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use \Sendinblue\Mailin as Mailin;

class APIController extends Controller
{
    // handle a successful payment
    public function doChargeSucceeded(Request $request)
    {
        /*
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));

        // Do something with $event_json
        $user = User::where('stripe_id',$request->customer)->first();

        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
        $data = array(
            "id" => 3,
            "to" => $user->email,
            "attr" => array('CUSTOMER' => $user->email,'LASTFOUR' => $request->source->last4, 'TRANSID' => $request->id,'DATE' => date('m-d-y g:i a',$request->created), 'AMOUNT' => '$'.substr($request->amount,0,-2))
        );

        $mailin->send_transactional_template($data);*/
    }
}
