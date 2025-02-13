<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;
    protected $table = 'work_experience';  // <-- Added this line
    protected $fillable = [
        'personal_info_id',
        'from',
        'to',
        'position_title',
        'department',
        'monthly_salary',
        'salary_grade',
        'step_increment',
        'appointment_status',
        'govt_service'
    ];
    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class);
    }
}
