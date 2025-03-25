<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder {
    public function run() {
        $departments = [
            'Office of the Mayor',
            'Office of the Vice Mayor',
            'Sangguniang Bayan (SB)',
            'Municipal Planning and Development Office (MPDO)',
            'Municipal Engineering Office',
            'Municipal Health Office (MHO)',
            'Municipal Treasurer’s Office',
            'Municipal Assessor’s Office',
            'Municipal Budget Office',
            'Municipal Accounting Office',
            'Municipal Civil Registrar’s Office',
            'Municipal Social Welfare and Development Office (MSWDO)',
            'Municipal Agriculture Office (MAO)',
            'Municipal Environment and Natural Resources Office (MENRO)',
            'Municipal Disaster Risk Reduction and Management Office (MDRRMO)',
            'Municipal Tourism Office',
            'Public Employment Service Office (PESO)',
            'Municipal Human Resource Management Office',
            'Municipal Legal Office',
            'Municipal Public Information Office (PIO)',
            'Municipal General Services Office (MGSO)',
            'Municipal Economic Enterprise Development Office (MEEDO)',
            'Municipal Information and Communications Technology Office (MICTO)',
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'department_name' => $department
            ]);
        }
    }
}
