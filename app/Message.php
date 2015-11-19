<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // set the table
    protected $table = 'messages';

    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
}
