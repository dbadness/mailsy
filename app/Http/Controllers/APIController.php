<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use \Sendinblue\Mailin as Mailin;

class APIController extends Controller
{
    // handle a successful payment
    public function doChargeSucceeded()
    {
        // make the arrays that you need to access the right values
        $stripe = json_decode($_POST,true);
        $transaction = $stripe['data']['object'];

        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));

        // Do something with $event_json
        $user = User::where('stripe_id',$transaction['customer'])->first();

        $mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));
        $data = array(
            "id" => 3,
            "to" => $user->email,
            "attr" => array('CUSTOMER' => $user->email,'LASTFOUR' => $transaction['source']['last4'], 'TRANSID' => $transaction['id'],'DATE' => date('m-d-y g:i a',$stripe['created'], 'AMOUNT' => '$'.substr($transaction['amount'],0,-2))
        );

        $mailin->send_transactional_template($data);

        return response($content);
    }
}
