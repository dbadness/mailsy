<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // set the table
    protected $table = 'customers';
    
    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
}
