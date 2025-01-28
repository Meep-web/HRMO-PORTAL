<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'employees';

    // Define the fillable fields (columns that can be mass-assigned)
    protected $fillable = [
        'userId',
        'employeeName',
        'role',
    ];

    // You can also define the primary key if it's not the default 'id'
    // protected $primaryKey = 'userId';
}
