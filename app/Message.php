<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;
use App\User;

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

        // check to see if the message was replied to
        $client = User::googleClient();
        $gmail = new \Google_Service_Gmail($client);
        $thread = $gmail->users_threads->get('me',$message->google_message_id);
        $messages = $thread->getMessages();
        $messageCount = count($messages);

        if($messageCount > 1)
        {
            $message->status = 'replied';
            $message->save();
        }

    	return $message->status;
    }
}
