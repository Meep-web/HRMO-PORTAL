<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CivilServiceEligibility extends Model
{
    use HasFactory;
    protected $table = 'civil_service_eligibility';
    protected $fillable = [
        'personal_info_id',
        'eligibility_name',
        'rating',
        'date_of_exam',
        'place_of_exam',
        'license_number',
        'license_validity'
    ];
    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class);
    }
}
