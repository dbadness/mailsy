<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use League\Csv\Reader;
use Log;

class Email extends Model
{
    // set the table
    protected $table = 'emails';
    
    // don't automitically add timestamps to new/updated records
    public $timestamps = false;

    public static function makeFieldList($user, $request)
    {

        // combine the subject and template for regex matching
        $content = $request->_subject.' '.$request->_email_template;

        // find the variables in the email and return them to the view        
        preg_match_all('/@@[a-zA-Z0-9]*/',$content,$matches);
        if($matches)
        {
            foreach($matches as $k => $v)
            {
                $fields = [];
                foreach($v as $match)
                {
                    // shave the delimiters
                    $field = trim($match,'@@');
                    $fields[] = $field;
                }
                $fields = array_unique($fields, SORT_REGULAR);
                return $fields;
            }
//            return redirect('/use/'.base64_encode($email->id));
        }
        else
        {
            $fields = [];
            return $fields;
        }

    }

    public static function deleteTempFieldList($email_id)
    {
        $email = Email::find($email_id);
        $email->temp_recipients_list = null;
        $email->save();
    }

    public static function makeNewEmail($user, $request, $oneOff)
    {

        $email = new Email;
        $email->user_id = $user->id;
        $email->name = $request->_name;
        $email->subject = $request->_subject;
        $email->template = $request->_email_template;
        $email->creator_name = $user->name;
        $email->created_at = time();
        $email->shared = 0;
        $email->copies = 0;
        if($oneOff)
        {
            $email->one_off = 1;
        }
        if($user->admin)
        {
            $email->creator_company = $user->id;
        } else
        {
            $email->creator_company = $user->belongs_to;
        }

        $fieldsOut = Email::makeFieldList($user, $request);

        // save the fields to the DB
        $email->fields = json_encode($fieldsOut);
        $email->save();

        return $email;

    }

    public static function updateEmail($user, $request)
    {
        $email = Email::find($request->_email_id);
        $email->name = $request->_name;
        $email->subject = $request->_subject;
        $email->template = $request->_email_template;

        $fields = Email::makeFieldList($user, $request);
        $email->fields = json_encode($fields);
        $email->save();

        return $email;
    }
    
}