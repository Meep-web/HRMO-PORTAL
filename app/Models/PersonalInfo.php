<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    use HasFactory;
    protected $table = 'personal_info';  // <-- Added this line
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'name_extension',
        'date_of_birth',
        'place_of_birth',
        'sex',
        'civil_status',
        'height',
        'weight',
        'blood_type',
        'gsis_id',
        'pagibig_id',
        'philhealth_id',
        'sss_no',
        'tin_no',
        'agency_employee_no',
        'telephone_no',
        'mobile_no',
        'email',
    ];

    // Define the relationships
    public function residentialAddress()
    {
        return $this->hasOne(Address::class, 'personal_info_id');
    }

    public function permanentAddress()
    {
        return $this->hasOne(Address::class, 'personal_info_id');
    }
}
