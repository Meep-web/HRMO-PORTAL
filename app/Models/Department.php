<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = ['department_name'];

    // Relationship with Employment
    public function employees()
    {
        return $this->hasMany(Employment::class, 'department_id');
    }
}
