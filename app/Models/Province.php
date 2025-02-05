<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'provinceCode';
    protected $table = 'provinces';

    /**
     * Disables incrementing for primary key
     * to avoid forcing convertion to integer
     * removing leading zero on PSGC.
     */
    public $incrementing = false;
}
