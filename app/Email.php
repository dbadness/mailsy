<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    // set the table
    protected $table = 'emails';

    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
}
