<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utils;

use App\User;

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

        // find the user in the DB and update their subscription id
        $user = User::where('stripe_id',$transaction['customer'])->first();

        // make the amount a string with the appropriate decimals
        $amount = '$'.($transaction['amount_due'] / 100);

        // send confirmation email
        $subject = 'Mailsy Invoice - Paid';
        $body = 'Your Mailsy subscription has been successfully paid for. Thank you for using Mailsy!';
        $body = '<ul><li>Transaction ID: '.$transaction['id'].'</li>';
        $body = '<li>Date: '.date('m-d-Y',$stripe['created']).'</li>';
        $body = '<li>Transaction ID: '.$amount.'</li>';

        Utils::sendEmail($user->email,$subject,$body);

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

        // make the amount a string with the appropriate decimals
        $amount = '$'.($transaction['amount_due'] / 100);

        // send confirmation email
        $subject = 'Mailsy Invoice - Declined';
        $body = 'There was a problem charging your credit card for your Mailsy subscription. Please log into Mailsy and use the Settings page to update your credit card.';
        $body = '<ul><li>Transaction ID: '.$transaction['id'].'</li>';
        $body = '<li>Date: '.date('m-d-Y',$stripe['created']).'</li>';
        $body = '<li>Transaction ID: '.$amount.'</li>';

        Utils::sendEmail($user->email,$subject,$body);

        return 'invoice_payment_failed';
    }
}
