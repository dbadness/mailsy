<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    // set the table
    protected $table = 'recipients';

    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
}
