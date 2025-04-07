<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    use HasFactory;

    protected $table = 'employment';

    protected $fillable = [
        'personalID',
        'department_id',
        'stepIncrement',
        'salaryGrade',
        'updatedBy',
        'designation_id',  // Add designation_id here
        'date_hired',      // Add date_hired here
        'dateOfEffectivity',  // Add date_of_effectivity here
        'dateReleased',       // Add date_released here
    ];
    
    
    

    // Relationship with PersonalInfo
    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class, 'personalID');
    }

    // Relationship with Department (if you have a departments table)
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
