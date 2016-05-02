<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    // set the table
    protected $table = 'teams';
    
    // don't automitically add timestamps to new/updated records
    public $timestamps = false;

}
