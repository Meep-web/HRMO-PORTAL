<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryGrade extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'salaryGrade';

    // The primary key associated with the table.
    protected $primaryKey = 'grade';

    // The attributes that are mass assignable.
    protected $fillable = [
        'grade', 'step1', 'step2', 'step3', 'step4', 'step5', 'step6', 'step7', 'step8'
    ];

    // Disable timestamps as this table doesn't use them
    public $timestamps = false;
}
