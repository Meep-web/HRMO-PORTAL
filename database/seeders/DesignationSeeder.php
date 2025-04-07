<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder {
    public function run() {
        $designations = [
            'Office of the Mayor' => ['Municipal Mayor', 'Executive Assistant'],
            'Office of the Vice Mayor' => ['Municipal Vice Mayor', 'Legislative Staff'],
            'Sangguniang Bayan (SB)' => ['SB Member', 'SB Secretary'],
            'Municipal Planning and Development Office (MPDO)' => ['MPDC', 'Planning Officer'],
            'Municipal Engineering Office' => ['Municipal Engineer', 'Engineering Aide'],
            'Municipal Health Office (MHO)' => ['Municipal Health Officer', 'Nurse', 'Midwife'],
            'Municipal Treasurer’s Office' => ['Municipal Treasurer', 'Revenue Collection Clerk'],
            'Municipal Assessor’s Office' => ['Municipal Assessor', 'Assessment Clerk'],
            'Municipal Budget Office' => ['Municipal Budget Officer', 'Budget Aide'],
            'Municipal Accounting Office' => ['Municipal Accountant', 'Accounting Clerk'],
            'Municipal Civil Registrar’s Office' => ['Municipal Civil Registrar', 'Registration Officer'],
            'Municipal Social Welfare and Development Office (MSWDO)' => ['MSWDO Head', 'Social Welfare Officer'],
            'Municipal Agriculture Office (MAO)' => ['Municipal Agriculturist', 'Agricultural Technologist'],
            'Municipal Environment and Natural Resources Office (MENRO)' => ['MENRO Head', 'Environment Management Specialist'],
            'Municipal Disaster Risk Reduction and Management Office (MDRRMO)' => ['MDRRMO Head', 'DRRM Officer'],
            'Municipal Tourism Office' => ['Tourism Officer', 'Tourism Assistant'],
            'Public Employment Service Office (PESO)' => ['PESO Manager', 'Employment Officer'],
            'Municipal Human Resource Management Office' => ['HRMO Head', 'HR Officer'],
            'Municipal Legal Office' => ['Municipal Legal Officer', 'Legal Assistant'],
            'Municipal Public Information Office (PIO)' => ['PIO Head', 'Information Officer'],
            'Municipal General Services Office (MGSO)' => ['GSO Head', 'Supply Officer'],
            'Municipal Economic Enterprise Development Office (MEEDO)' => ['MEEDO Head', 'Enterprise Development Officer'],
            'Municipal Information and Communications Technology Office (MICTO)' => ['MICTO Head', 'IT Officer'],
        ];

        foreach ($designations as $deptName => $titles) {
            // Get the department ID
            $departmentId = DB::table('departments')->where('department_name', $deptName)->value('id');

            foreach ($titles as $title) {
                DB::table('designations')->insert([
                    'department_id' => $departmentId,
                    'designation' => $title,
                ]);
            }
        }
    }
}
