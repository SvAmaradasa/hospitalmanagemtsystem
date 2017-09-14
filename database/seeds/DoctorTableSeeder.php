<?php

use App\Doctor;
use App\DoctorSpecialty;
use App\Enum\DoctorType;
use Illuminate\Database\Seeder;

class DoctorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'doctor1' => [
                'title' => 'Dr',
                'firstName' => 'OPD',
                'middleName' => ',',
                'lastName' => 'Doctor',
                'gender' => 'Male',
                'address' => 'Kelaniya',
                'city' => 'Kelaniya',
                'birthday' => '1968-03-29',
                'nic' => '880890200V',
                'telephoneNo' => '0472241901',
                'mobileNo' => '0759112268',
                'maritalStatus' => 'Single',
                'email' => 'opddoctor@biyagamaprivatehospital.com',
                'degree' => 'Bachelor of Medicine',
                'doctorType' => 'OPD',
                'hospital' => 'General Hospital',
                'fees' => '6000'
            ],
            'doctor2' => [
                'title' => 'Dr',
                'firstName' => 'Channelling',
                'middleName' => ',',
                'lastName' => 'Doctor',
                'gender' => 'Male',
                'address' => 'Kelaniya',
                'city' => 'Kelaniya',
                'birthday' => '1968-03-29',
                'nic' => '880890201V',
                'telephoneNo' => '0472241901',
                'mobileNo' => '0759112268',
                'maritalStatus' => 'Single',
                'email' => 'channellingdoctor@biyagamaprivatehospital.com',
                'degree' => 'Bachelor of Medicine',
                'doctorType' => 'Channelling',
                'hospital' => 'General Hospital',
                'fees' => '1350'
            ]

        ];

        foreach ($data as $doctorData) {
            $doctor = new Doctor();
            $doctor->fill($doctorData);

            if ($doctor->doctorType == DoctorType::CHANNELLING) {
                $doctor->doctorSpecialty()->associate(DoctorSpecialty::find(1));
            }

            $doctor->save();
        }
    }
}
