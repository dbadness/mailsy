<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use Auth;
use \Sendinblue\Mailin as Mailin;

class Utils extends Model
{
    // send out an email with send in blue
    public static function sendEmail($to,$subject,$body)
    {
    	// initialize SendinBlue
    	$mailin = new Mailin("https://api.sendinblue.com/v2.0",env('SENDINBLUE_KEY'));

    	// send out the email
    	$data = array(
    	    "id" => 5, // blank template
    	    "to" => $to,
    	    "attr" => array(
    	        "SUBJECT" => $subject,
    	        'BODY' => 'Hi there,<br><br>'.$body.'<br><br>If you have any questions, simply reply to this email and we\'d be happy to help.<br><br>Best,<br>The Mailsy Team'
    	    )
    	);

    	$mailin->send_transactional_template($data);
    }
}
