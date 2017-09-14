<?php

use App\Drug;
use Illuminate\Database\Seeder;

class DrugTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $drugs = array(
            array('name' => 'Paracetamol', 'weight' => '500mg', 'qty' => 100, 'availableForAll' => true, 'price' => 0.90),
            array('name' => 'Amoxicillin', 'weight' => '250mg', 'qty' => 100, 'availableForAll' => true, 'price' => 7),
            array('name' => 'Amoxicillin', 'weight' => '500mg', 'qty' => 100, 'availableForAll' => true, 'price' => 12),
            array('name' => 'Augmentin', 'weight' => '625mg', 'qty' => 100, 'availableForAll' => true, 'price' => 25),
            array('name' => 'Amoxil', 'weight' => '500mg', 'qty' => 100, 'availableForAll' => false, 'price' => 5.60),
        );

        foreach ($drugs as $drugData) {
            $drug = new Drug();
            $drug->fill($drugData);
            $drug->save();
        }
    }
}
