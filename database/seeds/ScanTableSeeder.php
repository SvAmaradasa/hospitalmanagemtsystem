<?php

use App\Scan;
use Illuminate\Database\Seeder;

class ScanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $scans = array(
            array('name' => 'Normal abdomen/KUB Scan', 'hospitalFee' => 450, 'doctorFee' => 1350),
            array('name' => 'TVS Scan', 'hospitalFee' => 600, 'doctorFee' => 1900),
            array('name' => 'Doppler Scan Legs(DVT)', 'hospitalFee' => 750, 'doctorFee' => 2250),
            array('name' => 'Doppler Scan Pregnancy', 'hospitalFee' => 750, 'doctorFee' => 2250),
            array('name' => 'Doppler Scan Kidney', 'hospitalFee' => 750, 'doctorFee' => 2250),
            array('name' => 'Thyroid Scan', 'hospitalFee' => 500, 'doctorFee' => 1500),
            array('name' => 'Breast Scan', 'hospitalFee' => 500, 'doctorFee' => 1500),
            array('name' => 'Scrotum (Male Scan)', 'hospitalFee' => 500, 'doctorFee' => 1500)
        );

        foreach ($scans as $scanData) {
            $scan = new Scan();
            $scan->fill($scanData);
            $scan->save();
        }
    }
}
