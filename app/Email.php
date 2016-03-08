<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    // set the table
    protected $table = 'emails';
    
    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
    
    /** filter the page for this specific user.
     *
     * @param $eid id str encrypted email id
     * @param $user_id int auth'd users id
     * @return $email Object the id of the email object
     */
    public static function processCSV($csvFile, $emailID)
    {
        $csv = array();
        $headers = array();

        $invalid = true;
        // get the contents of the text file and put it into an array
        $rows = array_map('str_getcsv', file($csvFile));
        
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
                        return redirect('/use/' . base64_encode($emailID) . '?columnMismatch=true&badEmails=false&missingColumns=false&droppedRows=false&invalidCSV=false&empty=false');
                    }
                }
            }
        }
        if ($invalid) {

            return redirect('/use/' . base64_encode($emailID) . '?invalidCSV=true&badEmails=false&missingColumns=false&droppedRows=false&columnMismatch=false&empty=false');
        }
		return [$csv, $headers];
    }
    
}
