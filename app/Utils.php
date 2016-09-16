<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use League\Csv\Reader;
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

    // build the SMTP transport and mailer for Swift Mailer
    public static function buildSmtpMailer($user,$password)
    {

        // build the transport mechanism
        $transport = \Swift_SmtpTransport::newInstance($user->smtp_server, $user->smtp_port, $user->smtp_protocol)
        ->setUsername($user->smtp_uname)
        ->setPassword($password);

        $mailer = \Swift_Mailer::newInstance($transport);

        return $mailer;
    }

    public static function processCSV($request, $email, $user)
    {

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
            // $messageText = substr($messageText,0,-4);
            // $messageText = substr($messageText,3);

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

        // save the tempRecipientsList to the email object for future use (if needed)
        $email->temp_recipients_list = json_encode($tempRecipientsList);
        $email->save();

        foreach($processedCSV['email'] as $recipientEmail)
        {
            if(!filter_var($recipientEmail,FILTER_VALIDATE_EMAIL))
            {
                $errors['badEmails'] = "true";
                return redirect('/use/'.base64_encode($email->id) . '?' . http_build_query($errors));
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
            // trim the <p> tags off the messageText if the used DIDN'T paste a match styles
            // (this needs to be fixed with a new JS editor that doesn't automatically insert <p> tags)
            // if(substr($messageText,0,3) == '<p>')
            // {
            //     $messageText = substr($messageText,0,-4);
            //     $messageText = substr($messageText,3);
            // }
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

    //truncates html safely considering tags, mainly for blade templates
    public static function truncateHtml($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                    // if tag is a closing tag
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                        unset($open_tags[$pos]);
                        }
                    // if tag is an opening tag
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length> $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if($total_length>= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }
 

}
