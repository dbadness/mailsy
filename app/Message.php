<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Message extends Model
{
    // set the table
    protected $table = 'messages';

    // don't automitically add timestamps to new/updated records
    public $timestamps = false;

    // given a message id, update the message statuses (sent, read, replied to, not delievered)
    public static function updateMessageStatus($id)
    {
    	// auth the user
    	$user = Auth::user();

    	// get the message object
    	$message = Message::find($id);

    	return $message->status;
    }
}
