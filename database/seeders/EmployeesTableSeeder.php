<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('employees')->insert([
            [
                'userId' => 'EMP001',
                'employeeName' => 'Admin',
                'role' => 'Admin',
                'password' => Hash::make('admin123'),
                'imagePath' => null, // ❌ Removed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'userId' => 'EMP002',
                'employeeName' => 'John Doe',
                'role' => 'Encoder',
                'password' => Hash::make('password123'),
                'imagePath' => 'employeeImage/JohnDoe.png', // ✅ Kept
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'userId' => 'EMP003',
                'employeeName' => 'Jane Smith',
                'role' => 'Employee',
                'password' => Hash::make('employee123'),
                'imagePath' => null, // ❌ Removed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'userId' => 'EMP004',
                'employeeName' => 'Michael Johnson',
                'role' => 'Employee',
                'password' => Hash::make('employee123'),
                'imagePath' => null, // ❌ Removed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'userId' => 'EMP005',
                'employeeName' => 'Emily Davis',
                'role' => 'Employee',
                'password' => Hash::make('employee123'),
                'imagePath' => null, // ❌ Removed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
