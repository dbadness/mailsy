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
    
    public static function processCSV($request, $email, $user)
    {
<<<<<<< HEAD
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
=======

        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        $errors = array(
            "badEmails" => 'false',
            "noHeaders" => 'false',
            "noEmailInHeaders" => 'false',
            "headerFieldMissing" => 'false',
            "rowsNotExtant" => 'false',
            "incompleteColumns" => 'false',
            "blankData" => 'false',
            'tooLarge' => 'false'
            );

        //Get a list of fields from request
        $fields = array();
        foreach($_POST as $k => $v)
        {
            if(($k != 'files') && (substr($k,0,1) != '_') && ($k != 'csvFile') || ($k == '_email'))
            {
                if($k == '_email'){
                    $fields[] = 'email';
                } else
                {
                    $fields[] = strtolower($k);
                }
            }
        }

        //Read in CSV
        $fullCsv = Reader::createFromPath($request->csvFile);

        //check if header row exists
        if(count($fullCsv->fetchOne()) < 0)
        {
            $errors['noHeaders'] = 'true';
            return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
        }

        //Get headers
        $headers = $fullCsv->fetchOne();

        $count = 0;
        $locater = array();

        $emailExists = false;
        foreach($headers as $i => $header){
            //Clean up headers
            $header = strtolower($header);

            //emails are handled differently
            if(strtolower($header) == 'emails' || strtolower($header) == 'email')
            {
                $headers[$i] = 'email';
                $emailExists = true;
            }

            //Check headers against fields
            foreach($fields as $field)
            {
                if($field == $header)
                {
                    //create a locater value to find things in headers by field name. Update counter.
                    $locater[$header] = $i;
                    $count++;
                } elseif($header == 'emails' || $header == 'email')
                {
                    $locater['email'] = $i;
                }
            }
        }

        //If email doesn't exist as any of the headers, error
        if(!$emailExists)
        {
            $errors["noEmailInHeaders"] = "true";
            return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
        }

        //If the number of fields doesn't equal, error
        if(count($fields) != $count)
        {
            $errors["headerFieldMissing"] = "true";
            return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
        }

        //Set CSV object sans headers
        $csv = $fullCsv->setOffset(1)->fetchAll();

        //make sure CSV exists below headers, or else error
        if(count($csv) < 1)
        {
            $errors["rowsNotExtant"] = "true";
            return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));

            if(count($csv[0]) < count($headers))
            {
            $errors["rowsNotExtant"] = "true";
            return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
            }
        }

        //check to see the values all exist and there aren't too many
        foreach($fields as $field)
        {
            foreach($csv as $i => $row)
            {
                if($i > env('MESSAGE_MAX')){
                    $errors['tooLarge'] = "true";
                    return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
                }
                if(count($row) < count($headers))
                {
                    $errors["incompleteColumns"] = "true";
>>>>>>> staging
                }
                if($row[$locater[$field]] == '')
                {
                    $errors["blankData"] = "true";
                    return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
                }
            }
        }

        //CSV has been approved

        //Build array of arrays named after headers and populate them
        $processedCSV = array();
        foreach($fields as $field)
        {
            $processedCSV[$field] = array();
            foreach($csv as $row)
            {
                array_push($processedCSV[$field], $row[$locater[$field]]);
            }
        }

        // build the recipient list and assign the fields to them
        $messages = [];
        $tempRecipientsList = [];
        foreach($processedCSV['email'] as $key => $recipientEmail)
        {

            // for each field provided, replace the variable in the template with the correct field input
            // use the key we returned from figuring out with recipient entry we're currently on
            $messageText = $request->_email_template;
            $subjectText = $request->_subject;
            $fieldEntries = [];
            foreach($fields as $field)
            {
                $subjectText = str_ireplace('@@'.$field, $processedCSV[$field][$key], $subjectText);
                $messageText = str_ireplace('@@'.$field, $processedCSV[$field][$key], $messageText);
                // set up an entry for the recipients list later on
                $fieldEntries[] = [$field => $processedCSV[$field][$key]];
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
            // let the db know that the message came from a csv
            $message->sent_with_csv = 'yes';

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
<<<<<<< HEAD
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

=======

>>>>>>> staging
        // save the tempRecipientsList to the email object for future use (if needed)
        $email->temp_recipients_list = json_encode($tempRecipientsList);
        $email->save();

<<<<<<< HEAD
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
=======
        foreach($processedCSV['email'] as $recipientEmail)
        {
            if(!filter_var($recipientEmail,FILTER_VALIDATE_EMAIL))
            {
                $errors['badEmails'] = "true";
                return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
>>>>>>> staging
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

                if(($k != 'files') && (substr($k,0,1) != '_') && ($k != 'csvFile'))
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