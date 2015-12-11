<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use \Sendinblue\Mailin as Mailin;

class APIController extends Controller
{
    // handle a successful payment (the first time)
    public function doInvoicePaid()
    {
        // Retrieve the request's body and parse it as JSON
        $input = @file_get_contents("php://input");
        $stripe = json_decode($input,true);
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
            "attr" => array(
                'CUSTOMER' => $user->email,
                'TRANSID' => $transaction['id'],
                'DATE' => date('m-d-Y',$stripe['created']), 
                'AMOUNT' => '$'.substr($transaction['lines']['data']['amount'],0,-2)
            )
        );

        $mailin->send_transactional_template($data);

        return 'invoice_successfully_paid';
    }

    // handle a successful payment (the first time)
    public function doInvoiceFailed()
    {
        // Retrieve the request's body and parse it as JSON
        $input = @file_get_contents("php://input");
        $stripe = json_decode($input,true);
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
            "attr" => array(
                'CUSTOMER' => $user->email,
                'TRANSID' => $transaction['id'],
                'DATE' => date('m-d-Y',$stripe['created']), 
                'AMOUNT' => '$'.substr($transaction['lines']['data']['amount'],0,-2)
            )
        );

        $mailin->send_transactional_template($data);

        return 'invoice_payment_failed';
    }
}
