<?php

use App\Lab;
use Illuminate\Database\Seeder;

class LabTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labs = array(
            array('name' => 'Urine Full Report', 'hospitalFee' => 400),
            array('name' => 'Lipid Profile', 'hospitalFee' => 1100),
            array('name' => 'Haemoglobin A1C (%HBAIC)', 'hospitalFee' => 700),
            array('name' => 'Fasting Plasma Glucose (FBS)', 'hospitalFee' => 500),
            array('name' => 'Erythrocyte Sedimentation Rate', 'hospitalFee' => 400),
            array('name' => 'Full Blood Count', 'hospitalFee' => 400),
            array('name' => 'Filaria antibody test', 'hospitalFee' => 500),
            array('name' => 'Urine sugar', 'hospitalFee' => 500),
            array('name' => 'Urea, electrolytes & creatinine', 'hospitalFee' => 750),
            array('name' => 'Faeces full report', 'hospitalFee' => 750),
            array('name' => 'Creatinine', 'hospitalFee' => 500),
            array('name' => 'CRP', 'hospitalFee' => 300),
            array('name' => 'ESR', 'hospitalFee' => 700),
            array('name' => 'TSH', 'hospitalFee' => 500),
            array('name' => 'Urea', 'hospitalFee' => 400),
            array('name' => 'Cholesterol', 'hospitalFee' => 1400)
        );

        foreach ($labs as $labData) {
            $lab = new Lab();
            $lab->fill($labData);
            $lab->save();
        }
    }
}
