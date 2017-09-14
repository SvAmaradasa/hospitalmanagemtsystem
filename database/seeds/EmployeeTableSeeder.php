<?php

use App\Employee;
use Illuminate\Database\Seeder;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'emp1' => [
                'title' => 'Mr',
                'firstName' => 'Administrator',
                'middleName' => '',
                'lastName' => '',
                'address' => '',
                'gender' => 'Male',
                'city' => '',
                'birthday' => '1988-03-29',
                'nic' => '880890202V',
                'telephoneNo' => '0472241901',
                'mobileNo' => '0759112268',
                'email' => 'admin@biyagamaprivatehospital.com'
            ],
            'emp2' => [
                'title' => 'Mr',
                'firstName' => 'Pharmacist',
                'middleName' => '',
                'lastName' => '',
                'address' => '',
                'gender' => 'Male',
                'city' => '',
                'birthday' => '1988-03-29',
                'nic' => '880890203V',
                'telephoneNo' => '0472241901',
                'mobileNo' => '0759112268',
                'email' => 'pharmacist@biyagamaprivatehospital.com'
            ]

        ];

        foreach ($data as $employeeData) {
            $employee = new Employee();
            $employee->fill($employeeData);
            $employee->save();
        }
    }
}
