<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NOSA extends Model
{
    use HasFactory;

    // Specify the table name (optional if the table name matches the model name)
    protected $table = 'NOSA';

    // Specify fillable columns (for mass assignment)
    protected $fillable = [
        'employeeName',
        'position',
        'department',
        'previousSalary',
        'newSalary',
        'dateOfEffectivity',
        'dateReleased',
        'salaryGrade',
        'stepIncrement',
        'userName',
    ];
}