<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'regionCode';

    /**
     * Disables incrementing for primary key
     * to avoid forcing convertion to integer
     * removing leading zero on PSGC.
     */
    public $incrementing = false;
}
