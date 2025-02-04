<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = array(
            array('provinceCode' => '0128','provinceName' => 'ILOCOS NORTE','regionCode' => '01'),
            array('provinceCode' => '0129','provinceName' => 'ILOCOS SUR','regionCode' => '01'),
            array('provinceCode' => '0133','provinceName' => 'LA UNION','regionCode' => '01'),
            array('provinceCode' => '0155','provinceName' => 'PANGASINAN','regionCode' => '01'),
            array('provinceCode' => '0209','provinceName' => 'BATANES','regionCode' => '02'),
            array('provinceCode' => '0215','provinceName' => 'CAGAYAN','regionCode' => '02'),
            array('provinceCode' => '0231','provinceName' => 'ISABELA','regionCode' => '02'),
            array('provinceCode' => '0250','provinceName' => 'NUEVA VIZCAYA','regionCode' => '02'),
            array('provinceCode' => '0257','provinceName' => 'QUIRINO','regionCode' => '02'),
            array('provinceCode' => '0308','provinceName' => 'BATAAN','regionCode' => '03'),
            array('provinceCode' => '0314','provinceName' => 'BULACAN','regionCode' => '03'),
            array('provinceCode' => '0349','provinceName' => 'NUEVA ECIJA','regionCode' => '03'),
            array('provinceCode' => '0354','provinceName' => 'PAMPANGA','regionCode' => '03'),
            array('provinceCode' => '0369','provinceName' => 'TARLAC','regionCode' => '03'),
            array('provinceCode' => '0371','provinceName' => 'ZAMBALES','regionCode' => '03'),
            array('provinceCode' => '0377','provinceName' => 'AURORA','regionCode' => '03'),
            array('provinceCode' => '0410','provinceName' => 'BATANGAS','regionCode' => '04'),
            array('provinceCode' => '0421','provinceName' => 'CAVITE','regionCode' => '04'),
            array('provinceCode' => '0434','provinceName' => 'LAGUNA','regionCode' => '04'),
            array('provinceCode' => '0456','provinceName' => 'QUEZON','regionCode' => '04'),
            array('provinceCode' => '0458','provinceName' => 'RIZAL','regionCode' => '04'),
            array('provinceCode' => '0505','provinceName' => 'ALBAY','regionCode' => '05'),
            array('provinceCode' => '0516','provinceName' => 'CAMARINES NORTE','regionCode' => '05'),
            array('provinceCode' => '0517','provinceName' => 'CAMARINES SUR','regionCode' => '05'),
            array('provinceCode' => '0520','provinceName' => 'CATANDUANES','regionCode' => '05'),
            array('provinceCode' => '0541','provinceName' => 'MASBATE','regionCode' => '05'),
            array('provinceCode' => '0562','provinceName' => 'SORSOGON','regionCode' => '05'),
            array('provinceCode' => '0604','provinceName' => 'AKLAN','regionCode' => '06'),
            array('provinceCode' => '0606','provinceName' => 'ANTIQUE','regionCode' => '06'),
            array('provinceCode' => '0619','provinceName' => 'CAPIZ','regionCode' => '06'),
            array('provinceCode' => '0630','provinceName' => 'ILOILO','regionCode' => '06'),
            array('provinceCode' => '0679','provinceName' => 'GUIMARAS','regionCode' => '06'),
            array('provinceCode' => '0712','provinceName' => 'BOHOL','regionCode' => '07'),
            array('provinceCode' => '0722','provinceName' => 'CEBU','regionCode' => '07'),
            array('provinceCode' => '0761','provinceName' => 'SIQUIJOR','regionCode' => '07'),
            array('provinceCode' => '0826','provinceName' => 'EASTERN SAMAR','regionCode' => '08'),
            array('provinceCode' => '0837','provinceName' => 'LEYTE','regionCode' => '08'),
            array('provinceCode' => '0848','provinceName' => 'NORTHERN SAMAR','regionCode' => '08'),
            array('provinceCode' => '0860','provinceName' => 'SAMAR (WESTERN SAMAR)','regionCode' => '08'),
            array('provinceCode' => '0864','provinceName' => 'SOUTHERN LEYTE','regionCode' => '08'),
            array('provinceCode' => '0878','provinceName' => 'BILIRAN','regionCode' => '08'),
            array('provinceCode' => '0972','provinceName' => 'ZAMBOANGA DEL NORTE','regionCode' => '09'),
            array('provinceCode' => '0973','provinceName' => 'ZAMBOANGA DEL SUR','regionCode' => '09'),
            array('provinceCode' => '0983','provinceName' => 'ZAMBOANGA SIBUGAY','regionCode' => '09'),
            array('provinceCode' => '0997','provinceName' => 'CITY OF ISABELA','regionCode' => '09'),
            array('provinceCode' => '1013','provinceName' => 'BUKIDNON','regionCode' => '10'),
            array('provinceCode' => '1018','provinceName' => 'CAMIGUIN','regionCode' => '10'),
            array('provinceCode' => '1035','provinceName' => 'LANAO DEL NORTE','regionCode' => '10'),
            array('provinceCode' => '1042','provinceName' => 'MISAMIS OCCIDENTAL','regionCode' => '10'),
            array('provinceCode' => '1043','provinceName' => 'MISAMIS ORIENTAL','regionCode' => '10'),
            array('provinceCode' => '1123','provinceName' => 'DAVAO DEL NORTE','regionCode' => '11'),
            array('provinceCode' => '1124','provinceName' => 'DAVAO DEL SUR','regionCode' => '11'),
            array('provinceCode' => '1125','provinceName' => 'DAVAO ORIENTAL','regionCode' => '11'),
            array('provinceCode' => '1182','provinceName' => 'COMPOSTELA VALLEY','regionCode' => '11'),
            array('provinceCode' => '1186','provinceName' => 'DAVAO OCCIDENTAL','regionCode' => '11'),
            array('provinceCode' => '1247','provinceName' => 'COTABATO (NORTH COTABATO)','regionCode' => '12'),
            array('provinceCode' => '1263','provinceName' => 'SOUTH COTABATO','regionCode' => '12'),
            array('provinceCode' => '1265','provinceName' => 'SULTAN KUDARAT','regionCode' => '12'),
            array('provinceCode' => '1280','provinceName' => 'SARANGANI','regionCode' => '12'),
            array('provinceCode' => '1298','provinceName' => 'COTABATO CITY','regionCode' => '12'),
            array('provinceCode' => '1339','provinceName' => 'NCR, CITY OF MANILA, FIRST DISTRICT','regionCode' => '13'),
            array('provinceCode' => '1374','provinceName' => 'NCR, SECOND DISTRICT','regionCode' => '13'),
            array('provinceCode' => '1375','provinceName' => 'NCR, THIRD DISTRICT','regionCode' => '13'),
            array('provinceCode' => '1376','provinceName' => 'NCR, FOURTH DISTRICT','regionCode' => '13'),
            array('provinceCode' => '1401','provinceName' => 'ABRA','regionCode' => '14'),
            array('provinceCode' => '1411','provinceName' => 'BENGUET','regionCode' => '14'),
            array('provinceCode' => '1427','provinceName' => 'IFUGAO','regionCode' => '14'),
            array('provinceCode' => '1432','provinceName' => 'KALINGA','regionCode' => '14'),
            array('provinceCode' => '1444','provinceName' => 'MOUNTAIN PROVINCE','regionCode' => '14'),
            array('provinceCode' => '1481','provinceName' => 'APAYAO','regionCode' => '14'),
            array('provinceCode' => '1507','provinceName' => 'BASILAN','regionCode' => '15'),
            array('provinceCode' => '1536','provinceName' => 'LANAO DEL SUR','regionCode' => '15'),
            array('provinceCode' => '1538','provinceName' => 'MAGUINDANAO','regionCode' => '15'),
            array('provinceCode' => '1566','provinceName' => 'SULU','regionCode' => '15'),
            array('provinceCode' => '1570','provinceName' => 'TAWI-TAWI','regionCode' => '15'),
            array('provinceCode' => '1602','provinceName' => 'AGUSAN DEL NORTE','regionCode' => '16'),
            array('provinceCode' => '1603','provinceName' => 'AGUSAN DEL SUR','regionCode' => '16'),
            array('provinceCode' => '1667','provinceName' => 'SURIGAO DEL NORTE','regionCode' => '16'),
            array('provinceCode' => '1668','provinceName' => 'SURIGAO DEL SUR','regionCode' => '16'),
            array('provinceCode' => '1685','provinceName' => 'DINAGAT ISLANDS','regionCode' => '16'),
            array('provinceCode' => '1740','provinceName' => 'MARINDUQUE','regionCode' => '17'),
            array('provinceCode' => '1751','provinceName' => 'OCCIDENTAL MINDORO','regionCode' => '17'),
            array('provinceCode' => '1752','provinceName' => 'ORIENTAL MINDORO','regionCode' => '17'),
            array('provinceCode' => '1753','provinceName' => 'PALAWAN','regionCode' => '17'),
            array('provinceCode' => '1759','provinceName' => 'ROMBLON','regionCode' => '17'),
            array('provinceCode' => '1845','provinceName' => 'NEGROS OCCIDENTAL','regionCode' => '18'),
            array('provinceCode' => '1846','provinceName' => 'NEGROS ORIENTAL','regionCode' => '18')
        );

        DB::table('provinces')->insert($provinces);
    }
}
