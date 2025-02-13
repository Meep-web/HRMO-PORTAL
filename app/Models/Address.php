<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $table = 'addresses';
    use HasFactory;

    protected $fillable = [
        'personal_info_id',
        'type',        // added 'type' to store whether it's 'residential' or 'permanent'
        'house_no',
        'street',
        'subdivision',
        'barangay',
        'city',
        'province',
        'zip_code'
    ];
    

    public function personalInfo()
    {
        return $this->belongsTo(PersonalInfo::class);
    }
}
