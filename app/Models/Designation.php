<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    // Table name (optional if table follows the Laravel conventions)
    protected $table = 'designations';

    // The attributes that are mass assignable (you can add more columns as needed)
    protected $fillable = [
        'department_id', 'designation'
    ];

    // Define the relationship with the Department model
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Define the relationship with the Employment model
    public function employments()
    {
        return $this->hasMany(Employment::class);
    }
}
