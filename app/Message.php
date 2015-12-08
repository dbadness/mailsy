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

    	// build the gmail client
    	$client = User::googleClient();
    	$service = new \Google_Service_Gmail($client);
    	$gmail = $service->users_messages->get('me', $message->google_message_id);

    	// see if the message was read
    	$labels = (array)$gmail->labelIds;
		if(!in_array('UNREAD', $labels))
    	{
    		$message->updated_at = time();
    		$message->status = 'read';
    		$message->save();
    	}

    	return $message->status;
    }
}
