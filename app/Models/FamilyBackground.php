<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyBackground extends Model
{
    use HasFactory;
    protected $table = 'family_background';
    protected $fillable = [
        'personal_info_id',
        'spouse_surname',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_name_extension',
        'spouse_occupation',
        'spouse_employer',
        'spouse_telephone',
        'father_surname',
        'father_first_name',
        'father_middle_name',
        'father_name_extension',
        'mother_surname',
        'mother_first_name',
        'mother_middle_name'
    ];

    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class);
    }
    
}
