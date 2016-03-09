<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    // set the table
    protected $table = 'emails';
    
    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
    
    public static function processCSV($request, $email, $user)
    {
        $errors = array("columnMismatch" => "false",
            "badEmails" => "false",
            "missingColumns" => "false",
            "droppedRows" => "false",
            "invalidCSV" => "false",
            "empty" => "false");

        $csv = array();

        $headers = array();

        $invalid = true;

        // get the contents of the text file and put it into an array
        $rows = array_map('str_getcsv', file($request->csvFile));

        foreach ($rows as $row) {
            //create an array for each header
            if ($row == $rows[0]) {
                foreach ($row as $header) {
                    $header = strtolower($header);
                    if ($header == 'emails') {
                        $header  = 'email';
                        $invalid = false;
                    } elseif ($header == 'email') {
                        $invalid = false;
                    }
                    $csv[$header] = array();
                    array_push($headers, $header);
                }
                //For the rest, populate the array with values
            } else {
                foreach ($headers as $key => $header) {
                    if (count($row) == count($headers)) {
                        array_push($csv[$header], $row[$key]);
                    } else {
                        $errors['columnMismatch'] = "true";
                        return redirect('/use/' . base64_encode($email->id) .'?'. http_build_query($errors));
                    }
                }
            }
        }
        if ($invalid) {
            $errors['invalidCSV'] = "true";
            return redirect('/use/' . base64_encode($email->id) . '?'. http_build_query($errors));
        }

        // build the recipient list and assign the fields to them
        $messages = [];
        $tempRecipientsList = [];

        // Add emails to email post
        $_POST['_email'] = array_merge($_POST['_email'], $csv['email']);

        foreach($_POST['_email'] as $key => $recipientEmail)
        {
            if($recipientEmail){

                // return the array of the fields from the user
                $fields = [];
                foreach($_POST as $k => $v)
                {
                    if(($k != 'files') && (substr($k,0,1) != '_') && ($k != 'csvFile'))
                    {
                        $fields[] = $k;
                    }
                }

                if(count($_POST['_email']) == 0){
                    if($csv){
                         if(count($csv) === 1 && $csv[0] === ''){
                            $errors['empty'] = "true";
                            return redirect('/use/'.base64_encode($email->id). '?' . http_build_query($errors));
                         }
                    } elseif(!$csv){
                        $errors['empty'] = "true";
                        return redirect('/use/'.base64_encode($email->id). '?' . http_build_query($errors));
                    }
                }

                if(count($_POST['_email']) == 0 || (count($csv) === 1 && $csv[0] === '')){
                   return redirect('/use/'.base64_encode($email->id).'?missingColumns=false&badEmails=false&droppedRows=false&columnMismatch=false&invalidCSV=false&empty=true');
                }

                // for each field provided, replace the variable in the template with the correct field input
                // use the key we returned from figuring out with recipient entry we're currently on
                $messageText = $request->_email_template;
                $subjectText = $request->_subject;
                $fieldEntries = [];

                $count = 0;
                foreach($fields as $field)
                {
                    foreach($headers as $header)
                    {
                        if ($field == $header)
                        {
                            $count++;
                        }
                    }
                }

                if($count != count($fields)){
                    $errors['missingColumns'] = "true";
                    return redirect('/use/'.base64_encode($email->id).'?'.http_build_query($errors));
                }

                //Append csv fields to existing requests so they're processed normally
                foreach($fields as $field)
                {
                    foreach($headers as $header)
                    {
                        if($header == $field)
                        {
                            $_POST[$field] = array_merge($_POST[$field], $csv[$header]);
                        }
                    }

                    $subjectText = str_replace('@@'.$field, $_POST[$field][$key], $subjectText);
                    $messageText = str_replace('@@'.$field, $_POST[$field][$key], $messageText);
                    // set up an entry for the recipients list later on
                    $fieldEntries[] = [$field => $_POST[$field][$key]];
                }

                // trim the <p> tags off the messageText
                $messageText = substr($messageText,0,-4);
                $messageText = substr($messageText,3);

                // make a message to throw into the DB
                $message = new Message;
                $message->user_id = $user->id;
                $message->email_id = $email->id;
                $message->recipient = $recipientEmail;
                $message->subject = $subjectText;
                if($request->_signature == 'on')
                {
                    $message->message = $messageText.'<br><br>'.$user->signature;
                }else
                {
                    $message->message = $messageText;
                }
                if($request->_send_to_salesforce == 'on')
                {
                    $message->send_to_salesforce = 'yes';
                }
                $message->created_at = time();
                $message->save();

                // set up the data list in case the user wants to go back and make some edits
                $tempRecipientsList[] = [
                    '_email' => $recipientEmail,
                    '_fields' => json_encode($fieldEntries)
                ];
            } else{
                if(count($_POST['_email']) == 0){
                    if($csv){
                         if(count($csv) === 1 && $csv[0] === ''){
                            $errors['empty'] = "true";
                            return redirect('/use/'.base64_encode($email->id).'?'. http_build_query($errors));
                         }
                    } elseif(!$csv){
                        $errors['empty'] = "true";
                        return redirect('/use/'.base64_encode($email->id).'?'. http_build_query($errors));
                    }
                }
                $dropped = true;
            }
        }

        // save the tempRecipientsList to the email object for future use (if needed)
        $email->temp_recipients_list = json_encode($tempRecipientsList);
        $email->save();

        // make sure the emails are legit
        foreach($request->_email as $recipientEmail)
        {
            if(!filter_var($recipientEmail,FILTER_VALIDATE_EMAIL))
            {
                if($dropped){
                    //set droppedRows if you want to track dropped
                      return redirect('/preview/'.base64_encode($email->id));

                } else{
                    $errors['badEmails'] = "true";
                    return redirect('/use/'.base64_encode($email->id).'?'.http_build_query($errors));
                }
            }
            else
            {
                return redirect('/preview/'.base64_encode($email->id));
            }
        }
    }

    public static function processManualData($request, $email, $user)
    {
        // build the recipient list and assign the fields to them
        $messages = [];
        $tempRecipientsList = [];
        foreach($_POST['_email'] as $key => $recipientEmail)
        {
            // return the array of the fields from the user
            $fields = [];
            foreach($_POST as $k => $v)
            {
                if(($k != 'files') && (substr($k,0,1) != '_'))
                {
                    $fields[] = $k;
                }
            }
            // for each field provided, replace the variable in the template with the correct field input
            // use the key we returned from figuring out with recipient entry we're currently on
            $messageText = $request->_email_template;
            $subjectText = $request->_subject;
            $fieldEntries = [];
            foreach($fields as $field)
            {
                $subjectText = str_replace('@@'.$field, $_POST[$field][$key], $subjectText);
                $messageText = str_replace('@@'.$field, $_POST[$field][$key], $messageText);
                // set up an entry for the recipients list later on
                $fieldEntries[] = [$field => $_POST[$field][$key]];
            }
            // trim the <p> tags off the messageText
            $messageText = substr($messageText,0,-4);
            $messageText = substr($messageText,3);
            // make a message to throw into the DB
            $message = new Message;
            $message->user_id = $user->id;
            $message->email_id = $email->id;
            $message->recipient = $recipientEmail;
            $message->subject = $subjectText;
            if($request->_signature == 'on')
            {
                $message->message = $messageText.'<br><br>'.$user->signature;
            }else
            {
                $message->message = $messageText;
            }
            if($request->_send_to_salesforce == 'on')
            {
                $message->send_to_salesforce = 'yes';
            }
            $message->created_at = time();
            $message->save();
            // set up the data list in case the user wants to go back and make some edits
            $tempRecipientsList[] = [
                '_email' => $recipientEmail,
                '_fields' => json_encode($fieldEntries)
            ];
        }
        // save the tempRecipientsList to the email object for future use (if needed)
        $email->temp_recipients_list = json_encode($tempRecipientsList);
        $email->save();
              // make sure the emails are legit
        foreach($request->_email as $recipientEmail)
        {
            if(!filter_var($recipientEmail,FILTER_VALIDATE_EMAIL))
            {
                return redirect('/edit/'.base64_encode($email->id).'?badEmails=true');
            }
            else
            {
                return redirect('/preview/'.base64_encode($email->id));
            }
        }
    }
    
}
