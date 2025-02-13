<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdsUpdate extends Model
{
    protected $table = 'pdsUpdates'; // Specify the table name
    protected $primaryKey = 'id'; // Specify the primary key

    protected $fillable = [
        'employeeName',
        'PDSId',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    //
}
