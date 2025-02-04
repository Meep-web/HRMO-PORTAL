<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = array(
            array('regionCode' => '01','regionName' => 'REGION I (ILOCOS REGION)'),
            array('regionCode' => '02','regionName' => 'REGION II (CAGAYAN VALLEY)'),
            array('regionCode' => '03','regionName' => 'REGION III (CENTRAL LUZON)'),
            array('regionCode' => '04','regionName' => 'REGION IV-A (CALABARZON)'),
            array('regionCode' => '05','regionName' => 'REGION V (BICOL REGION)'),
            array('regionCode' => '06','regionName' => 'REGION VI (WESTERN VISAYAS)'),
            array('regionCode' => '07','regionName' => 'REGION VII (CENTRAL VISAYAS)'),
            array('regionCode' => '08','regionName' => 'REGION VIII (EASTERN VISAYAS)'),
            array('regionCode' => '09','regionName' => 'REGION IX (ZAMBOANGA PENINSULA)'),
            array('regionCode' => '10','regionName' => 'REGION X (NORTHERN MINDANAO)'),
            array('regionCode' => '11','regionName' => 'REGION XI (DAVAO REGION)'),
            array('regionCode' => '12','regionName' => 'REGION XII (SOCCSKSARGEN)'),
            array('regionCode' => '13','regionName' => 'NATIONAL CAPITAL REGION (NCR)'),
            array('regionCode' => '14','regionName' => 'CORDILLERA ADMINISTRATIVE REGION (CAR)'),
            array('regionCode' => '15','regionName' => 'AUTONOMOUS REGION IN MUSLIM MINDANAO (ARMM)'),
            array('regionCode' => '16','regionName' => 'REGION XIII (Caraga)'),
            array('regionCode' => '17','regionName' => 'MIMAROPA'),
            array('regionCode' => '18','regionName' => 'NEGROS ISLAND REGION (NIR)')
        );
        
        DB::table('regions')->insert($regions);
    }
}
