<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ActionController extends Controller
{
    // return the fields to the new email view from the ajax call with template
    public function returnFields(Request $request)
    {
        // find the variables in the email and return them to the view        
        preg_match_all('/@@[a-zA-Z0-9]*/',$request->email,$matches);

        if($matches)
        {
            foreach($matches as $k => $v)
            {
                $fields = [];
                foreach($v as $match)
                {
                    // shave the delimiters
                    $field = trim($match,'@@');
                    $fields[] = strtolower($field);
                }
            }
            return json_encode($fields);
        }
        else
        {
            return 'No matches found.';
        }
    }
}
