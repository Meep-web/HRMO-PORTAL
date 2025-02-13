<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalBackground extends Model
{
    use HasFactory;
    protected $table = 'education';
    protected $fillable = [
        'personal_info_id',
        'level',
        'school',
        'degree',
        'from',
        'to',
        'highest_level',
        'year_graduated',
        'honors'
    ];

    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class);
    }
    
}
