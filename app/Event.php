<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // set the table
    protected $table = 'events';
    
    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
}
