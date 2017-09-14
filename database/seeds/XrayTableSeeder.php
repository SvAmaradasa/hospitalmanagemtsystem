<?php

use App\Xray;
use Illuminate\Database\Seeder;

class XrayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $xrays = array(
            array('name' => 'Chest X-ray', 'hospitalFee' => 900),
            array('name' => 'Back/Neck X-ray', 'hospitalFee' => 500),
            array('name' => 'Dental X-ray', 'hospitalFee' => 750),
            array('name' => 'Hand X-ray', 'hospitalFee' => 750),
            array('name' => 'Trauma X-ray - Lower limb', 'hospitalFee' => 750),
            array('name' => 'Foot X-ray', 'hospitalFee' => 750),
            array('name' => 'Cervical spine X-ray', 'hospitalFee' => 500),
            array('name' => 'Thoracic spine X-ray', 'hospitalFee' => 500),
            array('name' => 'Trauma X-ray - Upper limb', 'hospitalFee' => 750),
            array('name' => 'Skull X-ray', 'hospitalFee' => 750)
        );

        foreach ($xrays as $xrayData) {
            $xray = new Xray();
            $xray->fill($xrayData);
            $xray->save();
        }
    }
}
