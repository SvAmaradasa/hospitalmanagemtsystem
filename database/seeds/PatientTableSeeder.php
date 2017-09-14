<?php

use App\Patient;
use Illuminate\Database\Seeder;

class PatientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'title' => 'Mr',
            'firstName' => 'Patient1',
            'middleName' => 'Patient1',
            'lastName' => 'Patient1',
            'gender' => 'Male',
            'birthday' => '2010-01-01',
            'maritalStatus' => null,
            'address' => '"Udhana", Sitinamaluwa',
            'city' => 'Beliatta',
            'nic' => null,
            'telephoneNo' => null,
            'mobileNo' => null,
            'email' => 'biyagamaprivatehospital@gmail.com',
            'emergencyContact' => null,
            'emergencyContactPhone' => null,
            'emergencyContactRelation' => null,
            'employer' => null,
            'occupation' => null,
            'employerPhone' => null,
        ];

        $patient = new Patient();
        $patient->fill($data);
        $patient->save();
    }
}
