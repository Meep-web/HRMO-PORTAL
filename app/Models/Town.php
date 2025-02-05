<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'townCode';

    /**
     * Disables incrementing for primary key
     * to avoid forcing convertion to integer
     * removing leading zero on PSGC.
     */
    protected $fillable = ['town_name', 'town_code'];
    public $incrementing = false;
}