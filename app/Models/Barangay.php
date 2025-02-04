<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'barangayCode';

    /**
     * Disables incrementing for primary key
     * to avoid forcing convertion to integer
     * removing leading zero on PSGC.
     */
    protected $fillable = ['barangay_name', 'barangay_code'];
    public $incrementing = false;
}
