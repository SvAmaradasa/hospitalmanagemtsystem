<?php

use App\Fee;
use Illuminate\Database\Seeder;

class FeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fees = array(
            array('FeeType' => 'OPD', 'description' => 'Hospital Fee', 'fee' => 350.00, 'isVariable' => 0),
            array('FeeType' => 'Channelling', 'description' => 'Hospital Fee', 'fee' => 350.00, 'isVariable' => 0),
            array('FeeType' => 'Channelling', 'description' => 'Doctor Fee', 'fee' => null, 'isVariable' => 1),
            array('FeeType' => 'Scan', 'description' => 'Hospital Fee', 'fee' => null, 'isVariable' => 1),
            array('FeeType' => 'Scan', 'description' => 'Doctor Fee', 'fee' => null, 'isVariable' => 1),
            array('FeeType' => 'X-Ray', 'description' => 'Fee', 'fee' => null, 'isVariable' => 1),
            array('FeeType' => 'Lab', 'description' => 'Lab Test Fee', 'fee' => null, 'isVariable' => 1)
        );

        Fee::insert($fees);
    }
}
