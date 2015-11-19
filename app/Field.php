<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    // set the table
    protected $table = 'fields';

    // don't automitically add timestamps to new/updated records
    public $timestamps = false;
}
