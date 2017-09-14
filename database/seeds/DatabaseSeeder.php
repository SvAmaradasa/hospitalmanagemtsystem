<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Seed the database table in given order
         * */

        Model::unguard();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('users')->truncate();
        DB::table('employees')->truncate();
        DB::table('patients')->truncate();
        DB::table('doctor_specialties')->truncate();
        DB::table('doctors')->truncate();
        DB::table('fees')->truncate();
        DB::table('companies')->truncate();
        DB::table('appointment_details')->truncate();
        DB::table('appointments')->truncate();
        DB::table('invoices')->truncate();
        DB::table('payments')->truncate();
        DB::table('prescription_details')->truncate();
        DB::table('drugs')->truncate();
        DB::table('prescriptions')->truncate();
        DB::table('scans')->truncate();
        DB::table('xrays')->truncate();
        DB::table('labs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->call(EmployeeTableSeeder::class);
        $this->call(PatientTableSeeder::class);
        $this->call(DoctorSpecialtyTableSeeder::class);
        $this->call(DoctorTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(FeeTableSeeder::class);
        $this->call(DrugTableSeeder::class);
        $this->call(ScanTableSeeder::class);
        $this->call(XrayTableSeeder::class);
        $this->call(LabTableSeeder::class);
        Model::reguard();

    }
}
